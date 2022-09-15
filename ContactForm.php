<?php

class ContactForm
{
    protected $request = array();
    protected $msg = array();

    private $fillable = array(
        'name' => 'お名前',
        'tel' => '電話番号',
        'email' => 'メールアドレス',
        'detail' => 'お問い合わせ内容',
    );

    private $sendList = array(
        MASTER_MAIL,
    );

    public function __construct(array $rawRequest)
    {
        $this->request = FormRequest::validateFormRequest($rawRequest);
    }

    public function sendmail()
    {
        $this->judgeCSRFToken();

        mb_language("Japanese");
        mb_internal_encoding("UTF-8");

        $date = date('Y年m月d日 H時i分s秒');
        $agent = ($this->request['USER_AGENT']) ?: $_SERVER['HTTP_USER_AGENT'];
        $uri = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        $body = CreateMailBody::getBody($this->request, $date, $uri, $agent);

        $this->sendMailAdmin($body);
        $this->sendMailUser($body);

        header('Location: https://example.com');
        exit;
    }

    private function sendMailAdmin($body)
    {
        $subject = "ホームページからのお問い合わせ";
        $from = MASTER_MAIL;
        $replyTo = $this->request['email'];
        $replyUser = $this->request['name'];

        $header = "From: ". COMPANY_NAME . " <$from>\nReply-To: $replyUser <$replyTo>\n";

        foreach ($this->sendList as $address) {
            mb_send_mail($address, $subject, $body, $header);
        }
    }

    private function sendMailUser($body)
    {
        $subject = "お問い合わせありがとうございました。";
        $from = MASTER_MAIL;
        $header = "From: ". COMPANY_NAME . " <$from>\n";

        mb_send_mail($this->request['email'], $subject, $body, $header);
    }

    public function get_recaptcha_score($recaptcha_response)
    {
        $recaptcha = $this->curl_get_contents(RECAPTCHA_URI . '?secret=' . RECAPTCHA_SECRET_KEY . '&response=' . $recaptcha_response);
        $recaptcha = json_decode($recaptcha, false);

        return $recaptcha->score;
    }

    public function getErrorMessage()
    {
        $form_items = $this->fillable;

        foreach ($form_items as $name => $value) {
            if (empty($this->request[$name])) {
                $this->msg[] = "※{$value}を入力してください";
            }
        }

        return $this->msg;
    }

    private function judgeCSRFToken()
    {
        if ($this->request['csrfToken'] !== $_SESSION['csrfToken']) {
            header('Location:'. COMPANY_URI);
            exit;
        }
    }

    private function curl_get_contents($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}