<?php
namespace Ajir\RabbitMqSqlBundle\AMQP;

use Exception;
use InvalidArgumentException;
use JMS\Serializer\SerializerInterface;
use Ajir\RabbitMqSqlBundle\DataTransformer\DataTransformerInterface;
use Ajir\RabbitMqSqlBundle\Persister\PersisterInterface;
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
        LoggerInterface $logger = null,
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
     * @return bool Execution status (true if everything's of, false if message should be re-queued)
     * @throws Exception on applicative error
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

        if ($this->logger) {
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
        }

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
            if ($this->logger) {
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
        } catch (Exception $e) {
            if ($this->logger) {
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
            }
            throw $e;
        }

        return true;
    }
}
