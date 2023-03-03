<?php
namespace Vanier\Api\models;
use Vanier\Api\Models\BaseModel;

class ActorsModel extends BaseModel
{
    private string $table_name = 'films';
    public function createActors(array $actors){
        return $this->insert($this->table_name, $actors);
    }

    public function updateActor(array $actors){
        $this->update($this->table_name, $actors, ['film_id']);
    }
}

?>