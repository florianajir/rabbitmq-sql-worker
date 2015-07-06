<?php
namespace Meup\Bundle\SnotraBundle\AMPQ;

use InvalidArgumentException;
use JMS\Serializer\Serializer;
use Meup\Bundle\SnotraBundle\DataTransformer\DataTransformerInterface;
use Meup\Bundle\SnotraBundle\Manager\ManagerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

/**
 * Class SqlConsumer
 *
 * @author florianajir <florian@1001pharmacies.com>
 */
class SqlConsumer implements ConsumerInterface
{
    const DEFAULT_MESSAGE_CLASS = 'Meup\DataStructure\Message\AMPQMessage';
    const JSON_FORMAT = 'json';

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ManagerInterface
     */
    private $manager;

    /**
     * @var DataTransformerInterface
     */
    private $transformer;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @param DataTransformerInterface $transformer
     * @param ManagerInterface         $manager
     * @param Serializer               $serializer
     * @param LoggerInterface          $logger
     * @param string                   $msgClass
     * @param string                   $format
     */
    public function __construct(
        DataTransformerInterface $transformer,
        ManagerInterface $manager,
        Serializer $serializer,
        LoggerInterface $logger,
        $msgClass = self::DEFAULT_MESSAGE_CLASS,
        $format = self::JSON_FORMAT
    ) {
        $this->transformer = $transformer;
        $this->manager = $manager;
        $this->serializer = $serializer;
        $this->logger = $logger;
        $this->msgClass = $msgClass;
        $this->format = $format;
    }

    /**
     * @param AMQPMessage $message
     *
     * @return boolean Execution status (true if everything's of, false if message should be re-queued)
     */
    public function execute(AMQPMessage $message)
    {
        /* deserialize the message body */
        $message = $this
            ->serializer
            ->deserialize(
                $message->body,
                $this->msgClass,
                $this->format
            );

        $this
            ->logger
            ->info(
                'Message received from SQL Consumer',
                array(
                    'type' => $message->getType(),
                    'data' => $message->getData()
                )
            );

        //TODO check if this can happen like in es consumer and if it's useful (return true consume message)
        if (strtolower($message->getType()) === "locale") {
            return true;
        }

        try {
            $data = $this->transformer->prepare(
                $message->getType(),
                json_decode(
                    $message->getData(),
                    true
                )
            );
//            print_r($data);
//            ob_flush();
//            die;
            $this->manager->persist($data);
        } catch (InvalidArgumentException $e) {
            $this
                ->logger
                ->warning(
                    'Message not valid',
                    array(
                        'type'      => $message->getType(),
                        'data'      => $message->getData(),
                        'exception' => $e
                    )
                );
        }

        return true;
    }
}
