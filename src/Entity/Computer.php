<?php

namespace App\Entity;

use App\Repository\ComputerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ComputerRepository::class)
 */
class Computer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $label;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Processor;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $GraphicCard;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $Ram;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $Capacity;

    /**
     * @ORM\OneToMany(targetEntity=Booking::class, mappedBy="Computer")
     */
    private $bookings;

    public function __construct()
    {
        $this->bookings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getProcessor(): ?string
    {
        return $this->Processor;
    }

    public function setProcessor(?string $Processor): self
    {
        $this->Processor = $Processor;

        return $this;
    }

    public function getGraphicCard(): ?string
    {
        return $this->GraphicCard;
    }

    public function setGraphicCard(?string $GraphicCard): self
    {
        $this->GraphicCard = $GraphicCard;

        return $this;
    }

    public function getRam(): ?int
    {
        return $this->Ram;
    }

    public function setRam(?int $Ram): self
    {
        $this->Ram = $Ram;

        return $this;
    }

    public function getCapacity(): ?int
    {
        return $this->Capacity;
    }

    public function setCapacity(?int $Capacity): self
    {
        $this->Capacity = $Capacity;

        return $this;
    }

    /**
     * @return Collection|Booking[]
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): self
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings[] = $booking;
            $booking->setComputer($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): self
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getComputer() === $this) {
                $booking->setComputer(null);
            }
        }

        return $this;
    }
}
