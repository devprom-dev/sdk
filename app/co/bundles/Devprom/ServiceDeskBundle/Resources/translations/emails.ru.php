<?php
/**
 * Шаблоны писем
 *
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
return array(
    'issue.created.subject' => '[I-%id%] Ваша заявка передана в службу поддержки',
    'issue.created.body' => '
        <p>Здравствуйте!</p>

        <p>Вы добавили заявку "<a href="%link%">%title%</a>":</p>

        <blockquote>%description%</blockquote>
        <br/>
        <p><b>Тип:</b> %type%</p>
        <p><b>Продукт:</b> %product%</p>
        <p><b>Приоритет:</b> %priority%</p>
        <br/>
        <p>Специалисты службы поддержки уже занимаются вашей заявкой и свяжутся с Вами в ближайшее время.</p>
        <br/>
        <hr>
        <p>Вы можете просмотреть детали заявки и оставить свои комментарии по ссылке: <a href="%link%">%link%</a></p>
    ',

    'issue.updated.subject' => '[I-%id%] Ваша заявка была изменена',
    'issue.updated.body' => '
        <p>Здрaвcтвуйте!</p>
        <p>Ваша заявка "<a href="%link%">%title%</a>" была обновлена сотрудником службы поддержки:</p>
        %changes%
        <br/>
        <hr>
        <p>Вы можете просмотреть детали заявки и оставить свои комментарии по ссылке: <a href="%link%">%link%</a></p>
    ',

    'issue.commented.subject' => '[I-%id%] К вашей заявке оставлен комментарий',
    'issue.commented.body' => '
        <p>Здрaвcтвуйте!</p>
        <p>Сотрудник службы поддержки оставил комментарий к вашей заявке "<a href="%link%">%title%</a>":</p>
        <blockquote>%comment%</blockquote>
        <br/>
        <hr>
        <p>Вы можете просмотреть детали заявки и оставить свои комментарии по ссылке: <a href="%link%">%link%</a></p>
    ',

    'issue.resolved.subject' => '[I-%id%] Ваша заявка выполнена',
    'issue.resolved.body' => '
        <p>Здрaвcтвуйте!</p>
        <p>Ваша заявка "<a href="%link%">%title%</a>" была закрыта сотрудником службы поддержки</p>
        <p><b>Комментарий:</b></p>
        <blockquote>%comment%</blockquote>
        <br/>
        <hr>
        <p>Если Вы считаете, что заявка не выполнена или выполнена неполностью, то оставьте свои комментарии по ссылке: <a href="%link%">%link%</a></p>
    ',

    'resetting.email.subject' => 'Сброс пароля',
    'resetting.email.message' => '
            <p>Здравствуйте!</p>

            <p>Вы получили это письмо, потому что кто-то запросил восстановление пароля на сайте службы поддержки %clientName%.</p>
            <p>Для сброса пароля пожалуйста пройдите по ссылке: <a href="%confirmationUrl%">%confirmationUrl%</a></p>

            <p>С наилучшими пожеланиями,<br>
            Служба поддержки %clientName%</p>
            <a href="%link%">%link%</a>',

    'registration.email.subject' => 'Успешная регистрация в системе',
    'registration.email.message' => '
            <p>Здравствуйте!</p>

            <p>Благодарим Вас за регистрацию на сайте службы поддержки %clientName%.</p>
            <p>Ваш логин для входа на сайт: <i>%login%</i></p>
            <p>Ваш пароль: <i>%password%</i></p>

            <p>С наилучшими пожеланиями,<br>
            Служба поддержки %clientName%</p>
            <a href="%link%">%link%</a>',

);
?>
