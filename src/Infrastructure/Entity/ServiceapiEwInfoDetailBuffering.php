<?php

namespace App\Infrastructure\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ServiceapiEwInfoDetailBuffering
 *
 * @ORM\Table(name="serviceapi_ew_info_detail_buffering")
 * @ORM\Entity
 */
class ServiceapiEwInfoDetailBuffering
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="basic_id", type="integer", nullable=false)
     */
    private $basicId;

    /**
     * @var int
     *
     * @ORM\Column(name="is_archive", type="integer", nullable=false, options={"comment"="1 - archive, 0 - not"})
     */
    private $isArchive;

    /**
     * @var int
     *
     * @ORM\Column(name="is_incoming", type="integer", nullable=false, options={"comment"="1 - incoming, 0 - outgoing"})
     */
    private $isIncoming;

    /**
     * @var string
     *
     * @ORM\Column(name="sender_uuid_1c", type="string", length=255, nullable=false)
     */
    private $senderUuid1c;

    /**
     * @var string
     *
     * @ORM\Column(name="sender_phone", type="string", length=255, nullable=false)
     */
    private $senderPhone;

    /**
     * @var string
     *
     * @ORM\Column(name="sender_full_name", type="text", length=65535, nullable=false)
     */
    private $senderFullName;

    /**
     * @var string
     *
     * @ORM\Column(name="sender_first_name", type="string", length=255, nullable=false)
     */
    private $senderFirstName;

    /**
     * @var string
     *
     * @ORM\Column(name="sender_second_name", type="string", length=255, nullable=false)
     */
    private $senderSecondName;

    /**
     * @var string
     *
     * @ORM\Column(name="sender_last_name", type="string", length=255, nullable=false)
     */
    private $senderLastName;

    /**
     * @var string
     *
     * @ORM\Column(name="sender_company", type="text", length=65535, nullable=false)
     */
    private $senderCompany;

    /**
     * @var string
     *
     * @ORM\Column(name="receiver_uuid_1c", type="string", length=255, nullable=false)
     */
    private $receiverUuid1c;

    /**
     * @var string
     *
     * @ORM\Column(name="receiver_phone", type="string", length=255, nullable=false)
     */
    private $receiverPhone;

    /**
     * @var string
     *
     * @ORM\Column(name="receiver_full_name", type="text", length=65535, nullable=false)
     */
    private $receiverFullName;

    /**
     * @var string
     *
     * @ORM\Column(name="receiver_first_name", type="string", length=255, nullable=false)
     */
    private $receiverFirstName;

    /**
     * @var string
     *
     * @ORM\Column(name="receiver_second_name", type="string", length=255, nullable=false)
     */
    private $receiverSecondName;

    /**
     * @var string
     *
     * @ORM\Column(name="receiver_last_name", type="string", length=255, nullable=false)
     */
    private $receiverLastName;

    /**
     * @var string
     *
     * @ORM\Column(name="receiver_company", type="text", length=65535, nullable=false)
     */
    private $receiverCompany;

    /**
     * @var string
     *
     * @ORM\Column(name="sender_department_uuid_1c", type="string", length=255, nullable=false)
     */
    private $senderDepartmentUuid1c;

    /**
     * @var string
     *
     * @ORM\Column(name="sender_department_number", type="string", length=255, nullable=false)
     */
    private $senderDepartmentNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="sender_department_address", type="text", length=65535, nullable=false)
     */
    private $senderDepartmentAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="sender_department_city", type="string", length=255, nullable=false)
     */
    private $senderDepartmentCity;

    /**
     * @var string
     *
     * @ORM\Column(name="receiver_department_uuid_1c", type="string", length=255, nullable=false)
     */
    private $receiverDepartmentUuid1c;

    /**
     * @var string
     *
     * @ORM\Column(name="receiver_department_number", type="string", length=255, nullable=false)
     */
    private $receiverDepartmentNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="receiver_department_address", type="text", length=65535, nullable=false)
     */
    private $receiverDepartmentAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="receiver_department_city", type="string", length=255, nullable=false)
     */
    private $receiverDepartmentCity;

    /**
     * @var string
     *
     * @ORM\Column(name="ew_number", type="text", length=65535, nullable=false)
     */
    private $ewNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="client_number", type="text", length=65535, nullable=false)
     */
    private $clientNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="ttn", type="text", length=65535, nullable=false)
     */
    private $ttn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="order_date", type="datetime", nullable=false)
     */
    private $orderDate;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=false)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="delivery_type", type="string", length=10, nullable=false)
     */
    private $deliveryType;

    /**
     * @var string|null
     *
     * @ORM\Column(name="status_id", type="string", length=255, nullable=true)
     */
    private $statusId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="status_description", type="text", length=65535, nullable=true)
     */
    private $statusDescription;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="status_date", type="datetime", nullable=false)
     */
    private $statusDate;

    /**
     * @var string
     *
     * @ORM\Column(name="weight", type="string", length=100, nullable=false)
     */
    private $weight;

    /**
     * @var string
     *
     * @ORM\Column(name="max_size", type="string", length=50, nullable=false)
     */
    private $maxSize;

    /**
     * @var string
     *
     * @ORM\Column(name="type_size", type="string", length=100, nullable=false)
     */
    private $typeSize;

    /**
     * @var int
     *
     * @ORM\Column(name="count_cargo_places", type="integer", nullable=false)
     */
    private $countCargoPlaces;

    /**
     * @var string
     *
     * @ORM\Column(name="delivery_payment", type="string", length=50, nullable=false)
     */
    private $deliveryPayment;

    /**
     * @var string
     *
     * @ORM\Column(name="delivery_payment_received", type="string", length=50, nullable=false)
     */
    private $deliveryPaymentReceived;

    /**
     * @var int
     *
     * @ORM\Column(name="delivery_payment_payer", type="integer", nullable=false)
     */
    private $deliveryPaymentPayer;

    /**
     * @var int
     *
     * @ORM\Column(name="delivery_payment_status", type="integer", nullable=false)
     */
    private $deliveryPaymentStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="declared_cost", type="string", length=50, nullable=false)
     */
    private $declaredCost;

    /**
     * @var string
     *
     * @ORM\Column(name="cod_payment", type="string", length=50, nullable=false)
     */
    private $codPayment;

    /**
     * @var string
     *
     * @ORM\Column(name="cod_payment_received", type="string", length=50, nullable=false)
     */
    private $codPaymentReceived;

    /**
     * @var string
     *
     * @ORM\Column(name="cod_summ", type="string", length=100, nullable=false)
     */
    private $codSumm;

    /**
     * @var string
     *
     * @ORM\Column(name="cod_commission_external", type="string", length=100, nullable=false)
     */
    private $codCommissionExternal;

    /**
     * @var int
     *
     * @ORM\Column(name="cod_commission_external_payer", type="integer", nullable=false)
     */
    private $codCommissionExternalPayer;

    /**
     * @var int
     *
     * @ORM\Column(name="cod_is_available", type="integer", nullable=false)
     */
    private $codIsAvailable;

    /**
     * @var int
     *
     * @ORM\Column(name="cod_delivery_type", type="integer", nullable=false)
     */
    private $codDeliveryType;

    /**
     * @var string
     *
     * @ORM\Column(name="cod_card_number", type="string", length=100, nullable=false)
     */
    private $codCardNumber;

    /**
     * @var int
     *
     * @ORM\Column(name="cod_payment_status", type="integer", nullable=false)
     */
    private $codPaymentStatus;

    /**
     * @var int
     *
     * @ORM\Column(name="cod_commission_payment_status", type="integer", nullable=false)
     */
    private $codCommissionPaymentStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="cost_pay_sender", type="string", length=100, nullable=false)
     */
    private $costPaySender;

    /**
     * @var string
     *
     * @ORM\Column(name="cost_pay_receiver", type="string", length=100, nullable=false)
     */
    private $costPayReceiver;


}
