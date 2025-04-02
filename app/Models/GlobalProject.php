<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalProject extends Model {
    use HasFactory;

    protected $table = 'globales';

    protected $fillable = [
        'nombre',
        'valor',
        'tipo',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    protected $hidden = ['created_at', 'updated_at'];

    protected $appends = ['castValor'];

    public function getCastValorAttribute() {
        $castValor = '';
        switch ($this->tipo) {

        case 'number':
            $castValor = (float) $this->valor;
            break;
        case 'boolean':
            $castValor = (boolean) $this->valor;
            break;
        // case 'date':
        //     $castValor = $castValor = $this->valor;
        //     break;
        default:
            if (!in_array($this->tipo, ['string', 'date'])) {
                throw new \InvalidArgumentException("Tipo de dato desconocido: {$this->tipo}");
            }
            $castValor = trim($this->valor);
            break;
        }
        return $castValor;
    }
}
