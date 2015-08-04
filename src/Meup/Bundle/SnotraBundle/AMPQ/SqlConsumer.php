<?php
namespace Meup\Bundle\SnotraBundle\AMPQ;

use Exception;
use InvalidArgumentException;
use JMS\Serializer\SerializerInterface;
use Meup\Bundle\SnotraBundle\DataTransformer\DataTransformerInterface;
use Meup\Bundle\SnotraBundle\Persister\PersisterInterface;
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
     * @var PersisterInterface
     */
    private $persister;

    /**
     * @var DataTransformerInterface
     */
    private $transformer;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var array
     */
    private $ignoredTypes;

    /**
     * @param DataTransformerInterface $transformer
     * @param PersisterInterface       $persister
     * @param SerializerInterface      $serializer
     * @param LoggerInterface          $logger
     * @param array                    $ignoredTypes
     * @param string                   $msgClass
     * @param string                   $format
     */
    public function __construct(
        DataTransformerInterface $transformer,
        PersisterInterface $persister,
        SerializerInterface $serializer,
        LoggerInterface $logger,
        array $ignoredTypes = array(),
        $msgClass = self::DEFAULT_MESSAGE_CLASS,
        $format = self::JSON_FORMAT
    ) {
        $this->transformer = $transformer;
        $this->persister = $persister;
        $this->serializer = $serializer;
        $this->logger = $logger;
        $this->msgClass = $msgClass;
        $this->format = $format;
        $this->ignoredTypes = $ignoredTypes;
    }

    /**
     * @param AMQPMessage $message
     *
     * @return boolean Execution status (true if everything's of, false if message should be re-queued)
     */
    public function execute(AMQPMessage $message)
    {
        // deserialize the message body
        $message = $this
            ->serializer
            ->deserialize(
                $message->body,
                $this->msgClass,
                $this->format
            );

        // log message
        $this
            ->logger
            ->info(
                'Message received from SQL Consumer',
                array(
                    'type' => $message->getType(),
                    'data' => $message->getData()
                )
            );

        // return true consume message
        if (in_array(strtolower($message->getType()), $this->ignoredTypes)) {
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
            $this->persister->persist($data);
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
        } catch (Exception $e) {
            $this
                ->logger
                ->error(
                    'Exception in SQL Consumer',
                    array(
                        'type'      => $message->getType(),
                        'data'      => $message->getData(),
                        'exception' => $e
                    )
                );
            throw $e;
        }

        return true;
    }
}
