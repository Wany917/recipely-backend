<?php 
namespace Recipely\Models\formation;

use JetBrains\PhpStorm\Internal\ReturnTypeContract;
use PDO,Exception,PDOException;
use Recipely\Config\ConnectDb;
use Recipely\Utils\FormationExceptions;
use Recipely\Controllers\formation\Formation;

class FormationModel {
    private FormationExceptions $exception;

    public function __construct()
    {
        $this->exception = new FormationExceptions();
    }

    public function insertFormation(Formation $formation){
       try{
        $pdo = ConnectDb::getInstance();
        $stmt = $pdo->prepare(
            "INSERT INTO formations (id,name, description, id_provider, img) 
            VALUES (:id,:name, :description, :id_provider, :img)"
            );
        
        $params = [
            ':id' => $formation->toArray()['id'],
            ':name' => $formation->toArray()['name'],
            ':description' => $formation->toArray()['description'],
            ':id_provider' => $formation->toArray()['id_provider'],
            ':img' => $formation->toArray()['img']
        ];
        $stmt->execute($params);
        ConnectDb::closeConnexion();
        return $formation->toArray();
       }catch(Exception $e){
            ConnectDb::closeConnexion();
            return $e->getMessage();
       }
    }

    public function addRecipesFormations($formation_id, array $recipe_ids)
    {
        try {
            $pdo = ConnectDb::getInstance();
            $insertStmt = $pdo->prepare("INSERT INTO formations_recipes (formation_id, recipe_id) VALUES (:formation_id, :recipe_id)");

            foreach ($recipe_ids as $recipe_id) {
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM formations_recipes WHERE formation_id = :formation_id AND recipe_id = :recipe_id");
                $stmt->execute([
                    'formation_id' => $formation_id,
                    'recipe_id' => $recipe_id
                ]);
                $count = $stmt->fetchColumn();

                if ($count > 0) {
                    // Cette recette est déjà associée à la formation
                    continue;
                }

                $insertStmt->execute([
                    'formation_id' => $formation_id,
                    'recipe_id' => $recipe_id
                ]);
            }

            ConnectDb::closeConnexion();
        } catch (Exception $e) {
            ConnectDb::closeConnexion();
            throw $e;
        }
    }

    


    public function updateRecipeFormation(String $recipe_id,$formation_id){
        try{
            if($this->checkValue($recipe_id, 'id', 'recipes') && $this->checkValue($formation_id, 'id', 'formations')){
                return throw new FormationExceptions('Info not found check your parameters');
            }else{
                $pdo = ConnectDb::getInstance();
                $stmt = $pdo->prepare("UPDATE formations_recipes SET formation_id = :formation_id WHERE recipe_id = :recipe_id");
                
                $params = [
                    'formation_id' => $recipe_id,
                    'recipe_id' => $formation_id
                ];
                $stmt->execute($params);
    
                $stmt = $pdo->prepare("SELECT formation_id,recipe_id FROM formations_recipes WHERE formation_id = :formation_id AND recipe_id = :recipe_id");
                $stmt->execute($params);
                $formation_recipe = $stmt->fetch(PDO::FETCH_ASSOC);

                return $formation_recipe;
                ConnectDb::closeConnexion();
            }
        }catch(Exception $e){
            ConnectDb::closeConnexion();
            return $e->getMessage();
        }
    }

    public function removeRecipeFormation(String $recipe_id,$formation_id){
        try{
            if($this->checkValue($recipe_id, 'id', 'recipes') && $this->checkValue($formation_id, 'id', 'formations')){
                return 'Info not found check your parameters';
            }else{
                $pdo = ConnectDb::getInstance();
                $stmt = $pdo->prepare("DELETE FROM formations_recipes WHERE formation_id = :formation_id AND recipe_id = :recipe_id");
                
                $params = [
                    'formation_id' => $recipe_id,
                    'recipe_id' => $formation_id
                ];
                $stmt->execute($params);
                
                return 'Recipe removed from formation';
                ConnectDb::closeConnexion();
            }
    
        }catch(Exception $e){
            ConnectDb::closeConnexion();
            return $e->getMessage();
        }
    }

    public function updateFormation($id,$data_formation){
        try{
            $data_formation = json_decode($data_formation, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return 'Invalid JSON provided';
            }
    
            if (!isset($id)) {
                return 'No ID provided';
            }
    
            $pdo = ConnectDb::getInstance();
    
            // Check if the user exists
            $stmt = $pdo->prepare("SELECT name, description, id_provider, img FROM formations WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
    
            if ($stmt->rowCount() === 0) {
                return throw new FormationExceptions('error check your parameters');
            }
    
            $params = [':id' => $id];
            $updates = [];

            foreach ($data_formation as $key => $value) {
                if ($key !== 'id') {
                    $updates[] = $key . ' = :' . $key;
                    $params[':' . $key] = $value;
                }
            }

            if(count($updates) === 0){
                return throw new FormationExceptions('error check your parameters');
                ConnectDb::closeConnexion();
            } else {
                $sql = 'UPDATE formations SET ' . implode(', ', $updates) . ' WHERE id = :id';
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);

                $formation = $this->getFormation($id);
                return $formation;
                ConnectDb::closeConnexion();
            }
            ConnectDb::closeConnexion();
        } catch (Exception $e) {
            ConnectDb::closeConnexion();
            return $e->getMessage();
        }
    }

    public function getAllFormations(){
        try{
            $pdo = ConnectDb::getInstance();
            $stmt = $pdo->prepare("SELECT id, name, description, id_provider, img FROM formations");
            $stmt->execute();
            $formations = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if(count($formations) === 0){
                ConnectDb::closeConnexion();
                return false;
            } else {
                ConnectDb::closeConnexion();
                return $formations;
            }
        }catch(Exception $e){
            ConnectDb::closeConnexion();
            return $e->getMessage();
        }
    }

    public function getFormation($id){
        try{
            $pdo = ConnectDb::getInstance();
            $stmt = $pdo->prepare("SELECT id, name, description, id_provider, img FROM formations WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $formation = $stmt->fetch(PDO::FETCH_ASSOC);

            if(count($formation) === 0){
                ConnectDb::closeConnexion();
                return 'Not found check your parameter';
            } else {
                ConnectDb::closeConnexion();
                return $formation;
            }
        }catch(Exception $e){
            ConnectDb::closeConnexion();
            return $e->getMessage();
        }
    }

    public function getAllRecipeByFormations($id){
        try{
            $pdo = ConnectDb::getInstance();
            $stmt = $pdo->prepare(
                "SELECT 
                recipes.id,
                recipes.name,
                recipes.description,
                recipes.serves,
                recipes.prep_time,
                recipes.creator,
                recipes.img_md,
                recipes.img_sm,
                recipes.video 
                FROM recipes INNER JOIN formations_recipes 
                ON recipes.id = formations_recipes.recipe_id 
                WHERE formations_recipes.formation_id = :id"
            );
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if(count($recipes) === 0){
                ConnectDb::closeConnexion();
                return 'Not found check your parameter';
            } else {
                ConnectDb::closeConnexion();
                return $recipes;
            }
        }catch(Exception $e){
            ConnectDb::closeConnexion();
            return $e->getMessage();
        }
    }

    public function getRecipeByFormations($id,$recipe_id){
        try{
            $pdo = ConnectDb::getInstance();
            $stmt = $pdo->prepare(
                "SELECT 
                recipes.id,
                recipes.name,
                recipes.description,
                recipes.serves,
                recipes.prep_time,
                recipes.creator,
                recipes.img_md,
                recipes.img_sm,
                recipes.video 
                FROM recipes INNER JOIN formations_recipes 
                ON recipes.id = formations_recipes.recipe_id 
                WHERE formations_recipes.formation_id = :id 
                AND formations_recipes.recipe_id = :recipe_id"
            );
            $params = [
                'id' => $id,
                'recipe_id' => $recipe_id
            ];
            $stmt->execute($params);
            $recipe = $stmt->fetch(PDO::FETCH_ASSOC);

            if(count($recipe) === 0){
                ConnectDb::closeConnexion();
                return 'Not found check your parameter';
            } else {
                ConnectDb::closeConnexion();
                return $recipe;
            }
        }catch(Exception $e){
            ConnectDb::closeConnexion();
            return $e->getMessage();
        }
    }

    public function getFormationWithRecipes($id){
        try {
            $pdo = ConnectDb::getInstance();
            $stmt = $pdo->prepare(
                "SELECT 
                formations.id, 
                formations.name, 
                formations.description, 
                formations.id_provider, 
                formations.img, 
                
                recipes.id AS recipe_id, 
                recipes.name AS recipe_name, 
                recipes.description AS recipe_description, 
                recipes.serves AS recipe_serves, 
                recipes.prep_time AS recipe_prep_time, 
                recipes.creator AS recipe_creator, 
                recipes.img_md AS recipe_img_md, 
                recipes.img_sm AS recipe_img_sm, 
                recipes.video AS recipe_video 
                FROM formations 
                INNER JOIN formations_recipes ON formations.id = formations_recipes.formation_id 
                INNER JOIN recipes ON formations_recipes.recipe_id = recipes.id 
                WHERE formations.id = :id"
            );
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $formation = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            if(empty($formation)){
                ConnectDb::closeConnexion();
                throw new FormationExceptions($this->exception->notFound());
            } else {
                ConnectDb::closeConnexion();
                return $formation;
            }
        } catch(Exception $e){
            ConnectDb::closeConnexion();
            return $e->getMessage();
        }
    }
    
    public function deleteFormation($id){
        try {
            $pdo = ConnectDb::getInstance();
            $stmt = $pdo->prepare("SELECT id FROM formations WHERE id = :id");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $formation = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if(empty($formation)){
                ConnectDb::closeConnexion();
                return throw new FormationExceptions($this->exception->notFound());
            } else {
                $stmt = $pdo->prepare("DELETE FROM formations_recipes WHERE formation_id = :id");
                $stmt->bindValue(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
    
                $stmt = $pdo->prepare("DELETE FROM formations WHERE id = :id");
                $stmt->bindValue(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
    
                ConnectDb::closeConnexion();
                return true;
            }
        } catch(Exception $e){
            ConnectDb::closeConnexion();
            return $e->getMessage();
        }
    }

    public function checkValue($value, $column, $table){
        $pdo = ConnectDb::getInstance();
        $stmt = $pdo->prepare("SELECT $column FROM $table WHERE $column = :value");
        $stmt->execute(['value' => $value]);

        $fetch = $stmt->fetch(PDO::FETCH_ASSOC);
        ConnectDb::closeConnexion();

        if($fetch){
            return true;
        } else{
            return false;
        }
    }
}