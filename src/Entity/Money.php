<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MoneyRepository")
 */
class Money
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $time;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $currency;

    /**
     * @ORM\Column(type="float")
     */
    private $rate;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $growth;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $anomaly;


    public function __construct()
    {
        $this->setGrowth("0%");
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTime(): ?\DateTimeInterface
    {
        return $this->time;
    }

    /**
     * @param \DateTimeInterface $time
     * @return Money
     */
    public function setTime(\DateTimeInterface $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     * @return Money
     */
    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getRate(): ?float
    {
        return $this->rate;
    }

    /**
     * @param float $rate
     * @return Money
     */
    public function setRate(float $rate): self
    {
        $this->rate = $rate;

        return $this;
    }

    public function getGrowth(): ?string
    {
        return $this->growth;
    }

    /**
     * @param float|null $growth
     * @return Money
     */
    public function setGrowth(string $growth): self
    {
        $this->growth = $growth;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAnomaly()
    {
        return $this->anomaly;
    }

    /**
     * @param mixed $anomaly
     */
    public function setAnomaly($anomaly): void
    {
        $this->anomaly = $anomaly;
    }

    /**
     * @param array $data
     */
    public function hydrate(array $data){
        foreach ($data as $key => $value){
            $method = 'set' . ucfirst($key);

            // if the corresponding setter exists, we will call it.
            if (method_exists($this, $method)){
                if ($method != "setRate"){
                    $this->$method($value);
                }else{
                    $this->$method((float)$value);
                }
            }
        }
    }

}
