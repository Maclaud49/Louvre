<?php


namespace TicketingBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use TicketingBundle\Entity\Ticket;

/**
 * @ORM\Table(name="ticketing_order")
 * @ORM\Entity(repositoryClass="TicketingBundle\Repository\OrderRepository")
 * @ORM\HasLifecycleCallbacks()
 *
 * @UniqueEntity(fields="bookingCode", message="Ce code de réservation existe déjà.")
 */
class Order
{

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     * @Assert\Range(
     *     min = 1,
     *     max=100,
     *     minMessage ="La quantité doit être d'au moins 1.",
     *     maxMessage = "La quantité ne peut être supérieure à 100.")
     */
    private $quantity;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity(int $quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return string
     */
    public function getBookingCode()
    {
        return $this->bookingCode;
    }

    /**
     * @param string $bookingCode
     */
    public function setBookingCode(string $bookingCode)
    {
        $this->bookingCode = $bookingCode;
    }


    /**
     * @return mixed
     */
    public function getTickets()
    {
        return $this->tickets;
    }

    /**
     * @param Ticket $ticket
     */
    public function addTicket(Ticket $ticket)
    {
        $this->tickets->add($ticket);
        $ticket->setOrder($this);
    }

    public function removeTicket(Ticket $ticket)
    {
        $this->tickets->removeElement($ticket);
    }

    /**
     * @return \DateTime
     */
    public function getBookingDate()
    {
        return $this->bookingDate;
    }

    /**
     * @param \DateTime $bookingDate
     */
    public function setBookingDate(\DateTime $bookingDate)
    {
        $this->bookingDate = $bookingDate;
    }


    /**
     * @var string
     *
     * @ORM\Column(name="bookingCode", type="string", length=17, unique=true)
     */
    private $bookingCode;

    /**
     * @ORM\OneToMany(targetEntity="TicketingBundle\Entity\Ticket", mappedBy="order",cascade={"persist"})
     * @Assert\Valid()
     */
    private $tickets;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="bookingDate", type="date")
     */
    private $bookingDate;

    /**
     * @return float
     */
    public function getOrderAmount()
    {
        return $this->orderAmount;
    }

    /**
     * @param float $orderAmount
     */
    public function setOrderAmount(float $orderAmount)
    {
        $this->orderAmount = $orderAmount;
    }

    /**
     * @var double
     *
     * @ORM\Column(name="orderAmount", type="decimal")
     *
     */

    private $orderAmount;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="orderDateTime", type="datetime")
     *
     */
    private $orderDateTime;

    /**
     * @return \DateTime
     */
    public function getOrderDateTime()
    {
        return $this->orderDateTime;
    }

    /**
     * @param \DateTime $orderDateTime
     */
    public function setOrderDateTime(\DateTime $orderDateTime)
    {
        $this->orderDateTime = $orderDateTime;
    }


    public function __construct()
    {
        $this->bookingCode = $this->GenerateCode();
        $this->tickets = new ArrayCollection();
        $this->orderDateTime = new \DateTime();
    }

    private function GenerateCode()
    {
        $characts = 'abcdefghijklmnopqrstuvwxyz';
        $characts .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $characts .= '1234567890';
        $random_code = uniqid();

        for ($i = 0; $i < 4; $i++) {
            $random_code .= $characts[rand() % strlen($characts)];
        }
    return $random_code;
    }

}