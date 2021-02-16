<?php
include_once SERVER_ROOT_PATH.'cms/c_mail.php';

class MailBoxTest extends \PHPUnit\Framework\TestCase
{
    public function testEqualDomains() {
        $this->assertTrue(
            MailBox::compareDomains('asd@mail.ru', 'bde@mail.ru')
        );
        $this->assertTrue(
            MailBox::compareDomains('asd@mail.ru ', 'bde@mail.ru')
        );
        $this->assertTrue(
            MailBox::compareDomains('asd@mail.ru > ', 'bde@mail.ru>')
        );
        $this->assertTrue(
            MailBox::compareDomains('Thomas <asd@mail.ru > ', 'Barbara <bde@mail.ru>')
        );
    }
    public function testNonEqualDomains() {
        $this->assertFalse(
            MailBox::compareDomains('asd@demo.ru', 'bde@mail.ru')
        );
    }
    public function testParseAddressString() {
        list($email, $name) = HtmlMailBox::parseAddressString('devprom software support <support@devprom.ru>');
        $this->assertEquals('support@devprom.ru', $email);
        $this->assertEquals('devprom software support', $name);
    }
    public function testRemoveHiddenTags() {
        $this->assertEquals(
            '<meta> email body',
            TextUtils::getCleansedHtml('<style a="dd">ad</style><base>fwe</base><meta><!--[if !mso]><style>v\:* {behavior:url(#default#VML);}</style><![endif]--><style><!--@font-face--></style><!--[if gte mso 9]><xml></xml><![endif]--><!--[if gte mso 9]><xml></xml><![endif]--> email body')
        );
    }
}
