<?php
namespace SmsZilla\Adapter;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-08-05 at 11:36:21.
 */
class CiscoAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CiscoAdapter
     */
    protected $object;
    
    private $config = [];

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->config = include  __DIR__ . '/../../config.php';
        $this->object = new CiscoAdapter;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * ssh login and pass are not set
     * @covers SmsZilla\Adapter\CiscoAdapter::send
     * @covers SmsZilla\Adapter\AbstractAdapter::setParams
     * @covers \SmsZilla\ConfigurationException::__construct
     * @expectedException \SmsZilla\ConfigurationException
     * @expectedExceptionMessage SmsZilla\Adapter\CiscoAdapter is not configured properly. If SSH is enabled then parameters "ssh_host" and "ssh_login" must be set.
     */
    public function testSendConfigError() {
        $this->object->setParams(['use_ssh' => true]);
        // store_path is not set
        $this->object->send(new \SmsZilla\MessageModel);
    }
    
    /**
     * @covers SmsZilla\Adapter\CiscoAdapter::send
     * @covers SmsZilla\Adapter\AbstractAdapter::addError
     */
    public function testSend()
    {
        $message = new \SmsZilla\MessageModel();
        $message->setText($this->config['message']);
        $message->addRecipient($this->config['phones'][0]);
        $result = $this->object->send($message);
        $this->assertTrue($result);
        $this->assertCount(0, $this->object->getErrors());
    }
    
    /**
     * @covers SmsZilla\Adapter\CiscoAdapter::send
     * @covers SmsZilla\MessageModel::setText
     * @covers SmsZilla\MessageModel::addRecipient
     * @covers SmsZilla\Adapter\AbstractAdapter::setParams
     * @covers SmsZilla\Adapter\AbstractAdapter::getErrors
     * @covers SmsZilla\Adapter\AbstractAdapter::addError
     */
    public function testSendSsh()
    {
        $message = new \SmsZilla\MessageModel();
        $message->setText($this->config['message']);
        $message->addRecipient($this->config['phones'][0]);
        
        $this->object->setParams(['use_ssh' => true]);
        $this->object->setParams(['ssh_login' => 'dummy_user']);
        $this->object->setParams(['ssh_host' => '127.0.0.1']);
        
        $result = $this->object->send($message);
        $this->assertTrue($result);
        $this->assertCount(0, $this->object->getErrors());
    }

}