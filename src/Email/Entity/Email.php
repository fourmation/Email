<?php

namespace Email\Entity;

    /**
     * Class Email
     *
     * @package Email\Entity
     */
/**
 * Class Email
 * @package Email\Entity
 */
class Email
{
    /**
     * @var
     */
    protected $to;
    /**
     * @var
     */
    protected $from;
    /**
     * @var
     */
    protected $html;
    /**
     * @var
     */
    protected $text;
    /**
     * @var
     */
    protected $subject;
    /**
     * @var
     */
    protected $emailType;

    /**
     * @param  $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @return
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param  $vars
     */
    public function setVars($vars)
    {
        $this->vars = $vars;
    }

    /**
     * @return
     */
    public function getVars()
    {
        return $this->vars;
    }
    /**
     * @var
     */
    protected $template;
    /**
     * @var
     */
    protected $vars;


    /**
     * @param $body
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param $emailType
     */
    public function setEmailType($emailType)
    {
        $this->emailType = $emailType;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmailType()
    {
        return $this->emailType;
    }

    /**
     * @param $from
     */
    public function setFrom($from)
    {
        $this->from = $from;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param $to
     */
    public function setTo($to)
    {
        $this->to = $to;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTo()
    {
        return $this->to;
    }
    /**
     * @param  $html
     */
    public function setHtml($html)
    {
        $this->html = $html;
    }

    /**
     * @return
     */
    public function getHtml()
    {
        return $this->html;
    }

    /**
     * @param  $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return
     */
    public function getText()
    {
        return $this->text;
    }

}
