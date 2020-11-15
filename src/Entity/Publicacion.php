<?php

namespace App\Entity;

use App\Repository\PublicacionRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass=PublicacionRepository::class)
 * @ORM\Table(name="publicaciones")
 */
class Publicacion
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=45)
     */
    private $titulo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $descripcion;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $foto;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $pros;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $contras;

    /**
     * @ORM\Column(type="integer")
     */
    private $visitas;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity="Usuario", inversedBy="publicaciones")
     * @ORM\JoinColumn(name="id_usuario", referencedColumnName="id", nullable=false)
     */
    private $usuario;

    /**
     * @ORM\OneToMany(targetEntity="Comentario", mappedBy="publicacion")
     */
    protected $comentarios;


    public function __construct()
    {
        $this->contras = array();
        $this->pros = array();
        $this->comentarios = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): self
    {
        $this->titulo = $titulo;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getFoto(): ?string
    {
        return $this->foto;
    }

    public function setFoto(?string $foto): self
    {
        $this->foto = $foto;

        return $this;
    }

    public function getPros(): ?array
    {
        return $this->pros;
    }

    public function setPros(?array $pros): self
    {
        $this->pros = $pros;

        return $this;
    }

    public function getContras(): ?array
    {
        return $this->contras;
    }

    public function setContras(?array $contras): self
    {
        $this->contras = $contras;

        return $this;
    }

    public function getVisitas(): ?int
    {
        return $this->visitas;
    }

    public function setVisitas(int $visitas): self
    {
        $this->visitas = $visitas;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(Usuario $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * @return array
     */
    public function getComentarios()
    {
        return $this->comentarios;
    }

    /**
     * @param $comentario
     */
    public function addComentario($comentario)
    {
        $this->comentarios[] = $comentario;
    }

}
