<?php
namespace Email\Mapper;

use Email\Mapper\Exception\EmailException as EmailException;

use Zend\Mail;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

/**
 * Class Email
 *
 * @package Email\Mapper
 */
Class Email
{
    /**
     * @var bool - $isInitialized
     */
    protected $isInitialized = false;

    /**
     * @var
     */
    protected $zendMail;

    /**
     * @var
     */
    protected $entity;

    /**
     * @var
     */
    protected $pathStack;

    /**
     * @var
     */
    protected $config;

    /**
     * @var
     */
    protected $transport;
    /**
     * @var
     */
    protected $hydrator;

    /**
     * Performs some basic initialization setup and checks before running a query
     *
     * @return null
     */
    protected function initialize()
    {
        if ($this->isInitialized == true) {
            return;
        }

        if ($this->zendMail instanceof \Zend\Mail) {
            throw new EmailException('No zend mail adapter present');
        }

        if ($this->mimeMessage instanceof MimeMessage) {
            throw new EmailException('No mime message adapter present');
        }

        if ($this->mimePart instanceof MimePart) {
            throw new EmailException('No mime mail adapter present');
        }

        $this->isInitialized = true;
    }

    /**
     * @param string $type
     * @return string
     */
    function generateTemplate($type = 'html')
    {
        $entity = $this->getEntity();
        $config = $this->getConfig();
        $view = new \Zend\View\Renderer\PhpRenderer();
        $view->setResolver($this->pathStack);

        if (strlen($entity->getTemplate()) > 0) {
            $templatePath = 'email/';
            if (strpos($entity->getTemplate(), '/') === false) {
                $templatePath .= $entity->getTemplate();
            } else {
                $templatePath = $entity->getTemplate();
            }
        } else {
            $templatePath = 'email/default';
        }

        if ($type == 'html') {
            $content = $entity->getHtml();
        } else {
            $content = $entity->getText();
        }

        $viewModel = new \Zend\View\Model\ViewModel(
            array_merge(array('content' => $content), $entity->getVars())
        );
        $viewModel->setTemplate($templatePath);

        $layout = new \Zend\View\Model\ViewModel(array('content' => $view->render($viewModel), 'site_name' => $config['site_name']));
        $layout->setTerminal(true);
        $layout->setTemplate('layout/' . $type);

        return $view->render($layout);
    }

    /**
     * Sends an email with the options specified
     *
     * @param array $options
     * @throws EmailException
     */
    function send()
    {
        $entity = $this->getEntity();
        $config = $this->config;

        $defaultOptions = array(
            'htmlBody' => '',
            'textBody' => '',
            'subject' => 'Email',
            'from' =>$config['from_email'],
            'to' => $config['to_email']
        );

        $body = new MimeMessage();

        if ('html' == $entity->getEmailType()) {
            $htmlBody = $this->generateTemplate('html');
            $htmlPart = new MimePart($htmlBody);
            $htmlPart->type = "text/html";

            $partArray = array($htmlPart);

        } else if ('text' == $entity->getEmailType()) {
            $textBody = $this->generateTemplate('text');
            $textPart = new MimePart($textBody);
            $textPart->type = "text/plain";

            $partArray = array($textPart);
        } else {
            $textBody = $this->generateTemplate('text');
            $htmlBody = $this->generateTemplate('html');

            $htmlPart = new MimePart($htmlBody);
            $htmlPart->type = "text/html";
            $textPart = new MimePart($textBody);
            $textPart->type = "text/plain";

            $partArray = array($textPart, $htmlPart);
        }

        //handle attachments
        $attachments = $entity->getAttachment();
        if ( ! empty($attachments)) {
            $attachment = new MimePart(fopen($attachments['file'], 'r+'));
            $attachment->type = $attachments['type'];
            $attachment->disposition = 'attachment';
            $attachment->encoding = 'base64';
            $attachment->filename = 'test.xls';

            $partArray = array_merge($partArray, array($attachment));
        }

        $body->setParts($partArray);

        $from = (strlen($entity->getFrom()) > 0) ?  $entity->getFrom() : $config['from_email'];
        $to = $entity->getTo();

        $message = new Mail\Message();
        $message->setFrom($from);

        if ($config['development_mode']) {
            $to = $config['dev_email'];
        }

        $message->addTo($to);

        // no from or to address
        if ( ! $to || ! $from) {
            throw new \Application\Model\EmailException('Could not send email');
        }

        $message->setSubject($entity->getSubject());
        $message->setEncoding("UTF-8");
        $message->setBody($body);

        if ( ! empty($attachments)) {
            $message->getHeaders()->get('content-type')->setType('multipart/mixed');
        } else if (in_array($entity->getEmailType(), array('html', 'both'))) {
            $message->getHeaders()->get('content-type')->setType('text/html');
        }

        $transport = $this->getTransport();
        $transport->send($message);
    }

    /**
     * @param $subject
     * @return $this
     */
    public function subject($subject)
    {
        $entity = $this->getEntity();
        $entity->setSubject($subject);

        return $this;
    }

    /**
     * @param $html
     * @return $this
     */
    public function html($html)
    {
        $entity = $this->getEntity();
        $entity->setHtml($html);

        return $this;
    }

    /**
     * @param $text
     * @return $this
     */
    public function text($text)
    {
        $entity = $this->getEntity();
        $entity->setText($text);

        return $this;
    }

    /**
     * @param array $body
     * @return $this
     */
    public function body($body = array())
    {
        if (isset($body['html'])) {
            $this->html($body['html']);
        }
        if (isset($body['text'])) {
            $this->text($body['text']);
        }

        return $this;
    }

    /**
     * @param $to
     * @return $this
     */
    public function to($to)
    {
        $entity = $this->getEntity();
        $entity->SetTo($to);

        return $this;
    }

    /**
     * @param $from
     * @return $this
     */
    public function from($from)
    {
        $entity = $this->getEntity();
        $entity->setFrom($from);

        return $this;
    }

    /**
     * @param $type
     * @return $this
     * @throws Exception\EmailException
     */
    public function setType($type)
    {
        $entity = $this->getEntity();

        if ( ! in_array($type, array('html', 'text', 'both')) ) {
            throw new EmailException('Type can only be html, text or both');
        }

        $entity->setEmailType($type);

        return $this;
    }

    /**
     * @param $template
     * @return $this
     */
    public function setTemplate($template)
    {
        $entity = $this->getEntity();
        $entity->setTemplate($template);

        return $this;
    }

    /**
     * @param array $vars
     * @return $this
     */
    public function setVars($vars = array())
    {
        $entity = $this->getEntity();
        $entity->setVars($vars);

        return $this;
    }

    /**
     * @param $zendMail
     */
    public function setZendMail($zendMail)
    {
        $this->zendMail = $zendMail;
    }

    /**
     * @return mixed
     */
    public function getZendMail()
    {
        return $this->zendMail;
    }

    /**
     * @param  $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param $pathStack
     * @return $this
     */
    public function setPathStack($pathStack)
    {
        $this->pathStack = $pathStack;
        return $this;
    }

    /**
     * @param $config
     * @return $this
     */
    public function setConfig($config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param $transport
     * @return $this
     */
    public function setTransport($transport)
    {
        $this->transport = $transport;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTransport()
    {
        return $this->transport;
    }

    /**
     * @param array $attachmentOptions
     * @return $this
     */
    public function attachment($attachmentOptions)
    {
        $entity = $this->getEntity();
        $entity->setAttachment($attachmentOptions);

        return $this;
    }
}