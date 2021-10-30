<?php

namespace App\Entity;

use App\Repository\BookingRepository;
use App\Validator\AvailableBooking;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=BookingRepository::class)
 */
class Booking
{
    //tableau qui reprenste les creanau d'ouverture
    CONST TIMESLOT_ARRAY= array(
        '0' => false,
        '1' => array('08:00','16:00'),
        '2' => array('08:00','16:00'),
        '3' => array('08:00','12:00'),
        '4' => array('08:00','16:00'),
        '5' => array('08:00','16:00'),
        '6' => false,
        );
    CONST WEEKDAY= array("Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi","Vendredi","Samedi");
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */

    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $endDate;

    /**
     * @ORM\Column(type="boolean")
     */
    private $Cancel;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $User;

    /**
     * @ORM\ManyToOne(targetEntity=Computer::class, inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Computer;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $comment;

    public function __construct()
    {

    }

    public function getId(): ?int
    {
        return $this->id;
    }



    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getCancel(): ?bool
    {
        return $this->Cancel;
    }

    public function setCancel(bool $Cancel): self
    {
        $this->Cancel = $Cancel;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): self
    {
        $this->User = $User;

        return $this;
    }

    public function getComputer(): ?Computer
    {
        return $this->Computer;
    }

    public function setComputer(?Computer $Computer): self
    {
        $this->Computer = $Computer;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
//
    public function isTimeSlotValid(): bool
    {
                                                                                                                                                                                          //
        $day = $this->startDate->format('w');
        $timeSlot = self::TIMESLOT_ARRAY[$day];
        if (!$timeSlot){
            return false;
        } else {
            if($this->startDate->format('H:i') >= $timeSlot[0] &
                $this->endDate->format('H:i') <= $timeSlot[1])
                return true;
            else
                return false;
        }
    }

    public function initStartAndEndDate(){
        $this->startDate = new \DateTime();
        $dt = new \DateTime();
        $dt->add(new \DateInterval('PT1H')); // add 1 hour for the default hour
        $this->endDate = $dt;

    }
}
