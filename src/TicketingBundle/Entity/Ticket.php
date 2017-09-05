<?php


namespace TicketingBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="ticketing_ticket")
 * @ORM\Entity(repositoryClass="TicketingBundle\Repository\TicketRepository")
 * @ORM\HasLifecycleCallbacks()
 *
 */
class Ticket{
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
     * @ORM\ManyToOne(targetEntity="TicketingBundle\Entity\Order", inversedBy="tickets")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     */
    private $order;

    /**
     * @return mixed
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param Order $order
     */
    public function setOrder(Order $order)
    {
        $this->order = $order;
    }
    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getReducedPrice()
    {
        return $this->reducedPrice;
    }

    /**
     * @param mixed $reducedPrice
     */
    public function setReducedPrice($reducedPrice)
    {
        $this->reducedPrice = $reducedPrice;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price)
    {
       $today =date("Y-m-d");
       $birthday=$this->getBirthdayDate();
       $dateTime1=new \Datetime($today);
       $age = $dateTime1->diff($birthday)->format('%y');
       $reduced = $this->getReducedPrice();

       if($reduced){
           $this->price = 10;
       }
       elseif ($age<4){
           $this->price = 0;
       }
       elseif ($age>4 && $age<12){
           $this->price = 8;
       }
       else{
           $this->price = $price;
       }


    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return \DateTime
     */
    public function getBirthdayDate()
    {
        return $this->birthdayDate;
    }

    /**
     * @param \DateTime $birthdayDate
     */
    public function setBirthdayDate(\DateTime $birthdayDate)
    {
        $this->birthdayDate = $birthdayDate;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $country
     */
    public function setCountry(string $country)
    {
        $this->country = $country;
    }

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="fullDay", type="boolean")
     */
    private $type;

    /**
     * @ORM\Column(name="reducedPrice", type="boolean",options={"default":false})
     */
    private $reducedPrice;

    /**
     * @ORM\Column(name="ticket_price", type="decimal")
     */
    private $price;

    /**
     * @var string
     * @ORM\Column(name="lastName", type="string", length=255)
     * @Assert\Length(min=2,max=20,minMessage="Veuillez saisir au moins 2 lettres",maxMessage="50 lettres maximum")
     */
    private $lastName;

    /**
     * @var string
     * @ORM\Column(name="firstName", type="string", length=255)
     * @Assert\Length(min=2,max=20,minMessage="Veuillez saisir au moins 2 lettres",maxMessage="50 lettres maximum")
     */
    private $firstName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birthday_date", type="datetime")
     * @Assert\DateTime(
     *     message="La date n'est pas valide"
     * )
     */
    private $birthdayDate;

    /**
     * @var string
     * @ORM\Column(name="country", type="string", length=255)
     * @Assert\Length(min=2,max=20,minMessage="Veuillez saisir au moins 2 lettres",maxMessage="50 lettres maximum")
     */
    private $country;

    public function __construct()
    {
    }

    /**
     * @ORM\PrePersist
     */
    public function setTicketPrice(){
        $this->setPrice(16);
    }
}