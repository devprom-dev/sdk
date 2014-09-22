<?php
/**
 * Шаблоны писем
 *
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
return array(
    'issue.created.subject' => '[I-%id%] Your request has been submitted to support team',
    'issue.created.body' => '
        <p>Hi!</p>

        <p>You have created new request "<a href="%link%">%title%</a>":</p>

        <blockquote>%description%</blockquote>
        <br/>
        <p><b>Type:</b> %type%</p>
        <p><b>Product:</b> %product%</p>
        <p><b>Priority:</b> %priority%</p>
        <br/>
        <p>Our support engineers already working on your request and will get back to you in the nearest time.</p>
        <br/>
        <hr>
        <p>You can see your request and leave your comments here: <a href="%link%">%link%</a></p>
    ',

    'issue.updated.subject' => '[I-%id%] Your request has been updated',
    'issue.updated.body' => '
        <p>Hi!</p>
        <p>You request "<a href="%link%">%title%</a>" have been updated by support engineer:</p>
        %changes%
        <br/>
        <hr>
        <p>You can see your request and leave your comments here: <a href="%link%">%link%</a></p>
    ',

    'issue.commented.subject' => '[I-%id%] Your request has been commented',
    'issue.commented.body' => '
        <p>Hi!</p>
        <p>Support engineer have just left new comment to your request "<a href="%link%">%title%</a>":</p>
        <blockquote>%comment%</blockquote>
        <br/>
        <hr>
        <p>You can see your request and leave your comments here: <a href="%link%">%link%</a></p>
    ',

    'issue.resolved.subject' => '[I-%id%] Your request has been completed',
    'issue.resolved.body' => '
        <p>Hi!</p>
        <p>Your request "<a href="%link%">%title%</a>" have been completed</p>
        <p><b>Comment from support engineer:</b></p>
        <blockquote>%comment%</blockquote>
        <br/>
        <hr>
        <p>If you think your request still not complete, please leave a comment here: <a href="%link%">%link%</a></p>
    ',

    'resetting.email.subject' => 'Reset Password',
    'resetting.email.message' => '
            <p>Hello!</p>

            <p>You\'ve got this message, because someone has requested password recovery from %clientName% Support site.</p>
            <p>To reset your password please visit <a href="%confirmationUrl%">%confirmationUrl%</a></p>

            <p>Regards,<br>
            %clientName% Support</p>
            <a href="%link%">%link%</a>',

    'registration.email.subject' => 'Successful registration',
    'registration.email.message' => '
            <p>Hello!</p>

            <p>Thank you for registration on %clientName% Support site.</p>
            <p>Your login: <i>%login%</i></p>
            <p>Your password: <i>%password%</i></p>

            <p>Regards,<br>
            %clientName% Support</p>
            <a href="%link%">%link%</a>',

);
?>
