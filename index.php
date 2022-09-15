<?php

include_once 'contactForm.php';
$csrfToken = $msg = null;

if ($_REQUEST['_submit']) {
    $contact_form = new ContactForm($_REQUEST);
    $msg = $contact_form->getErrorMessage();

    // RECAPTCHAを設置していれば以下のチェックを行う
    if ($contact_form->get_recaptcha_score($_POST['recaptcha_response']) < 0.5) {
        $msg[] = '※不正なリクエストです';
    }

    if (!count($msg)) {
        $contact_form->sendmail();
    }
} else {
    $TOKEN_LENGTH = 16;
    $tokenByte = openssl_random_pseudo_bytes($TOKEN_LENGTH);
    $csrfToken = bin2hex($tokenByte);
    $_SESSION['csrfToken'] = $csrfToken;
}
?>

<section>
    <?php if (count($msg)): ?>
        <?php echo '<div class="message">' . implode('<br>', $msg) . '</div>'; ?>
    <?php endif; ?>
    <form method="post" class="h-adr">
        <input type="hidden" name="csrfToken" value="<?php echo $csrfToken; ?>">
        <span class="p-country-name" style="display:none;">Japan</span>
        <table>
            <tr>
                <th>
                    お名前
                </th>
                <td>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($_REQUEST['name']); ?>">
                </td>
            </tr>
            <tr>
                <th>
                    TEL
                </th>
                <td>
                    <input type="text" name="tel" value="<?php echo htmlspecialchars($_REQUEST['tel']); ?>">
                </td>
            </tr>
            <tr>
                <th>
                    メールアドレス
                </th>
                <td>
                    <input type="text" name="email" value="<?php echo htmlspecialchars($_REQUEST['email']); ?>">
                </td>
            </tr>
            <tr>
                <th>
                    お問い合わせ内容
                </th>
                <td>
                    <textarea name="note"><?php echo htmlspecialchars($_REQUEST['note']); ?></textarea>
                </td>
            </tr>
        </table>
        <input type="hidden" name="recaptcha_response" id="recaptchaResponse">
        <input type="hidden" name="csrfToken" id="csrf" value="<?php echo htmlspecialchars($csrfToken); ?>">
        <input type="submit" name="_submit" value="送信">
    </form>
    </div>
</section>
<script src="https://www.google.com/recaptcha/api.js?render=<?php echo RECAPTCHA_SITE_KEY; ?>"></script>
<script>
    grecaptcha.ready(function () {
        grecaptcha.execute('<?php echo RECAPTCHA_SITE_KEY; ?>', {action: 'contact'}).then(function (token) {
            var recaptchaResponse = document.getElementById('recaptchaResponse');
            recaptchaResponse.value = token;
        });
    });
</script>