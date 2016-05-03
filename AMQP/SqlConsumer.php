<?php
namespace Ajir\RabbitMqSqlBundle\AMQP;

use Ajir\RabbitMqSqlBundle\DataStructure\Message\AMQPMessageInterface;
use Ajir\RabbitMqSqlBundle\DataTransformer\DataTransformerInterface;
use Ajir\RabbitMqSqlBundle\Persister\PersisterInterface;
use Exception;
use InvalidArgumentException;
use JMS\Serializer\SerializerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

/**
 * Class SqlConsumer
 *
 * @author Florian Ajir <florianajir@gmail.com>
 */
class SqlConsumer implements ConsumerInterface
{
    const DEFAULT_MESSAGE_CLASS = 'Ajir\RabbitMqSqlBundle\DataStructure\Message\AMQPMessage';
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
     * @var string
     */
    private $msgClass;

    /**
     * @var string
     */
    private $format;

    /**
     * @param DataTransformerInterface $transformer
     * @param PersisterInterface $persister
     * @param SerializerInterface $serializer
     * @param array $ignoredTypes
     * @param string $msgClass
     * @param string $format
     */
    public function __construct(
        DataTransformerInterface $transformer,
        PersisterInterface $persister,
        SerializerInterface $serializer,
        array $ignoredTypes = array(),
        $msgClass = self::DEFAULT_MESSAGE_CLASS,
        $format = self::JSON_FORMAT
    )
    {
        $this->transformer = $transformer;
        $this->persister = $persister;
        $this->serializer = $serializer;
        $this->msgClass = $msgClass;
        $this->format = $format;
        $this->ignoredTypes = $ignoredTypes;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param AMQPMessage $message
     *
     * @return bool Execution status (true if everything's of, false if message should be re-queued)
     * @throws Exception on applicative error
     */
    public function execute(AMQPMessage $message)
    {
        $message = $this->deserializeMessage($message);
        $this->logReceivedMessage($message);
        $consume = true;
        $type = $message->getType();
        $data = $message->getData();
        if (false === $this->isTypeIgnored($type)) {
            try {
                $assoc = json_decode($data, true);
                $data = $this->transformer->prepare($type, $assoc);
                $this->persister->persist($data);
            } catch (InvalidArgumentException $exception) {
                $this->logMessageInvalid($message, $exception);
                $consume = false;
            } catch (Exception $exception) {
                $this->logMessageError($message, $exception);
                throw $exception;
            }
        }

        return $consume;
    }

    /**
     * @param string $type
     * @return bool
     */
    private function isTypeIgnored($type)
    {
        return in_array(strtolower($type), $this->ignoredTypes);
    }

    /**
     * @param AMQPMessage $message
     * @return AMQPMessageInterface
     */
    private function deserializeMessage(AMQPMessage $message)
    {
        return $this
            ->serializer
            ->deserialize(
                $message->getBody(),
                $this->msgClass,
                $this->format
            );
    }

    /**
     * @param AMQPMessageInterface $message
     */
    private function logReceivedMessage(AMQPMessageInterface $message)
    {
        if (null !== $this->logger) {
            // log message
            $this->logger->info(
                'Message received from SQL Consumer',
                array(
                    'type' => $message->getType(),
                    'data' => $message->getData()
                )
            );
        }
    }

    /**
     * @param AMQPMessageInterface $message
     * @param InvalidArgumentException $exception
     */
    private function logMessageInvalid(AMQPMessageInterface $message, InvalidArgumentException $exception)
    {
        if (null !== $this->logger) {
            $this->logger->warning(
                'Message invalid',
                array(
                    'type' => $message->getType(),
                    'data' => $message->getData(),
                    'exception' => $exception
                )
            );
        }
    }

    /**
     * @param AMQPMessageInterface $message
     * @param Exception $exception
     */
    private function logMessageError(AMQPMessageInterface $message, Exception $exception)
    {
        if (null !== $this->logger) {
            $this->logger->error(
                'Consumer SQL Exception',
                array(
                    'type' => $message->getType(),
                    'data' => $message->getData(),
                    'exception' => $exception
                )
            );
        }
    }
}
