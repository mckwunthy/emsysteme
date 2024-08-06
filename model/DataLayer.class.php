<?php
//all function needed to get data from or communicate with mysql database via sql request

class DataLayer
{

    private $connexion;

    function __construct() //connexion to db with API PDO
    {
        try {
            $this->connexion = new PDO("mysql:host=" . HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
            //echo "connexion à la base de données réussie";
        } catch (PDOException $th) {
            echo $th->getMessage();
        }
    }


    /**
     * fonction qui sert à récupérer les membre au sein de la base de données okgetEventById
     * @param rien ne prend pas de paramètre
     * @return array tableau contenant les genres, en cas de succès, FALSE sinon
     * @return NULL s'il y a une exception déclenchée 
     */
    function getMember()
    {
        $sql = "SELECT * FROM users";
        try {
            $result = $this->connexion->prepare($sql);
            $var = $result->execute();
            $data = $result->fetchAll(PDO::FETCH_ASSOC);
            if ($data) {
                return $data;
            } else {
                return FALSE;
            }
        } catch (PDOException $th) {
            return NULL;
        }
    }

    /**
     * fonction qui sert à récupérer les evenements au sein de la base de données
     * @param rien ne prend pas de paramètre
     * @return array tableau contenant les livre, en cas de succès, FALSE sinon
     * @return NULL s'il y a une exception déclenchée 
     */
    function getEvents()
    {
        $sql = "SELECT * FROM event";
        try {
            $result = $this->connexion->prepare($sql);
            $var = $result->execute();
            $data = $result->fetchAll(PDO::FETCH_ASSOC);
            if ($data) {
                return $data;
            } else {
                return FALSE;
            }
        } catch (PDOException $th) {
            return NULL;
        }
    }

    /**
     * fonction qui créer un evenement en base de données ok
     * @param name le titre du livre
     * @param lieu le lieu
     * @param eventDate la date
     * @param heure l'heure
     * @param description la description
     * @param image le nouveau nom de l'image
     * @return TRUE si creation réalisée avec succès, FALSE sinon
     * @return NULL s'il y a une exception déclenchée 
     */
    function createEvents($name, $lieu, $date, $heure, $description, $owner_id, $image)
    {
        $sql = "INSERT INTO `event`(`name`, `lieu`, `date`, `heure`, `description`, `owner_id` ,`image`) VALUES (:name, :lieu, :date, :heure, :description, :owner_id, :image)";
        // print_r($sql);
        // exit();
        try {
            $result = $this->connexion->prepare($sql);
            $var = $result->execute(array(
                'name' => $name,
                'lieu' => $lieu,
                'date' => $date,
                'heure' => $heure,
                'description' => $description,
                'owner_id' => $owner_id,
                'image' => $image
            ));
            if ($var) {
                return TRUE;
            } else {
                return FALSE;
            }
        } catch (PDOException $th) {
            return NULL;
        }
    }
    /**
     * fonction qui mettre à jour le user OK
     * @param email l'email'
     * @param fname le firstname
     * @param lname Le lastname
     * @param age l'age
     * @param sexe le sexe
     * @param description la descripton
     * @return TRUE si en cas la mise à jour s'effectue avec succès, FALSE sinon
     * @return NULL s'il y a une exception déclenchée 
     */
    function updatUser($email, $fname, $lname, $age, $sexe, $description)
    {
        $sql = "UPDATE users SET f_name = :f_name, l_name = :l_name, age = :age, sexe = :sexe, description = :description WHERE email = :email";
        // print_r($sql);
        // exit();
        try {
            $result = $this->connexion->prepare($sql);
            $var = $result->execute(array(
                'f_name' => $fname,
                'l_name' => $lname,
                'age' => $age,
                'sexe' => $sexe,
                'description' => $description,
                'email' => $email
            ));
            if ($var) {
                $data = array(
                    'f_name' => $fname,
                    'l_name' => $lname,
                    'age' => $age,
                    'sexe' => $sexe,
                    'description' => $description,
                    'email' => $email
                );
                return $data;
            } else {
                return FALSE;
            }
        } catch (PDOException $th) {
            return NULL;
        }
    }

    /**
     * fonction qui sert à récupérer les livres au sein de la base de données selon isbn OK
     * @param id l'id de l'évènement
     * @return array tableau contenant les livre, en cas de succès, FALSE sinon
     * @return NULL s'il y a une exception déclenchée 
     */
    function getEventById($id)
    {
        $sql = "SELECT * FROM event WHERE id = :id";
        try {
            $result = $this->connexion->prepare($sql);
            $var = $result->execute(array(':id' => $id));
            $data = $result->fetchAll(PDO::FETCH_ASSOC);
            if ($data) {
                return $data;
            } else {
                return FALSE;
            }
        } catch (PDOException $th) {
            return NULL;
        }
    }
    /**
     * fonction qui sert à récupérer les reservation OK
     * @param id l'id de l'évènement
     * @return array tableau contenant les livre, en cas de succès, FALSE sinon
     * @return NULL s'il y a une exception déclenchée 
     */
    function getBooking()
    {
        $sql = "SELECT * FROM booking";
        try {
            $result = $this->connexion->prepare($sql);
            $var = $result->execute();
            $data = $result->fetchAll(PDO::FETCH_ASSOC);
            if ($data) {
                return $data;
            } else {
                return FALSE;
            }
        } catch (PDOException $th) {
            return NULL;
        }
    }
    /**
     * fonction qui sert à supprimer un event de la base de données selon isbn OK
     * @param id id de l'évènement a supprimer
     * @return TRUE si la commande a été exécutée, FALSE sinon
     * @return NULL s'il y a une exception déclenchée 
     */
    function deleteEventById($event_id)
    {
        $sql_1 = "DELETE FROM booking WHERE event_id = :event_id";
        $sql_2 = "DELETE FROM event WHERE id = :id";

        try {
            $result_1 = $this->connexion->prepare($sql_1);
            $var_1 = $result_1->execute(array(':event_id' => $event_id));

            $result_2 = $this->connexion->prepare($sql_2);
            $var_2 = $result_2->execute(array(':id' => $event_id));

            if ($var_2) {
                return TRUE;
            } else {
                return FALSE;
            }
        } catch (PDOException $th) {
            return NULL;
        }
    }


    /**
     * fonction qui créer une demande de reservation OK
     * @param event_booking_id le id de l'évènement
     * @param id_user_booking Le id du demandeur de reservation
     * @return TRUE si en cas de demande réalisée avec succès, FALSE sinon
     * @return NULL s'il y a une exception déclenchée 
     */
    function createBooking($event_booking_id, $id_user_booking)
    {
        $sql = "INSERT INTO `booking`(`event_id`, `user_id`) VALUES (:event_booking_id, :id_user_booking)";
        // print_r($sql);
        // exit();
        try {
            $result = $this->connexion->prepare($sql);
            $var = $result->execute(array(
                'event_booking_id' => $event_booking_id,
                'id_user_booking' => $id_user_booking
            ));

            if ($var) {
                return TRUE;
            } else {
                return FALSE;
            }
        } catch (PDOException $th) {
            return NULL;
        }
    }


    /**
     * fonction qui valide une demande de reservation OK
     * @param booking_id id de la demande
     * @return TRUE si en cas de validation réalisée avec succès, FALSE sinon
     * @return NULL s'il y a une exception déclenchée 
     */
    function updatBookingAskingList($booking_id)
    {
        $sql = "UPDATE booking SET status = 1 WHERE id = :booking_id";

        try {
            $result = $this->connexion->prepare($sql);
            $var = $result->execute(array(
                ':booking_id' => $booking_id
            ));
            // print_r($var);
            // exit();
            if ($var) {
                return TRUE;
            } else {
                return FALSE;
            }
        } catch (PDOException $th) {
            return NULL;
        }
    }

    /**
     * fonction qui permet d'authentifier un user
     * @param email l'email du customer
     * @param password le mot de passe du customer
     * @return ARRAY tableau contenant les infos du user si authentification réussie
     * @return FALSE si authentification échouée
     * @return NULL s'il y a une exception déclenchée 
     */
    function authentifier($email, $password)
    {
        $sql = "SELECT * FROM users WHERE email = :email";
        try {
            $result = $this->connexion->prepare($sql);
            $result->execute(array(':email' => $email));
            $data = $result->fetch(PDO::FETCH_ASSOC);
            if ($data && ($data['password'] == sha1($password))) {
                unset($data['password']);
                if ($data['sexe'] == 1) {
                    $data['sexe'] = "masculin";
                } else {
                    $data['sexe'] = "feminin";
                }
                return $data;
            } else {
                return FALSE;
            }
        } catch (PDOException $th) {
            return NULL;
        }
    }

    /**
     * fonction qui met à jour les informations du user OK
     * @param nameEvent le nom de l'évènement
     * @param lieu le lieu
     * @param eventDate la date
     * @param heure l'heure
     * @param description la description
     * @param owner_id l'id du promoteur
     * @param event_id l'id de l'évènement 
     * @return TRUE si en cas de commande réalisée avec succès, FALSE sinon
     * @return NULL s'il y a une exception déclenchée 
     */
    function updatEvents($nameEvent, $lieu, $eventDate, $heure, $description, $owner_id, $event_id, $eventImage)
    {


        $sql = "UPDATE event SET name = :nameEvent, lieu = :lieu, date= :eventDate, heure = :heure, description = :description, owner_id = :owner_id WHERE id= :event_id";
        // print_r($sql);
        // exit();
        try {
            $result = $this->connexion->prepare($sql);
            $var = $result->execute(array(
                'nameEvent' => $nameEvent,
                'lieu' => $lieu,
                'eventDate' => $eventDate,
                'heure' => $heure,
                'description' => $description,
                'owner_id' => $owner_id,
                'event_id' => $event_id
            ));

            $data = array(
                'nameEvent' => $nameEvent,
                'lieu' => $lieu,
                'eventDate' => $eventDate,
                'heure' => $heure,
                'description' => $description,
                'owner_id' => $owner_id,
                'event_id' => $event_id,
                'image' => $eventImage
            );

            if ($var) {
                return $data;
            } else {
                return FALSE;
            }
        } catch (PDOException $th) {
            return NULL;
        }
    }

    /**
     * fonction qui créer un user en base de données OK
     * @param email l'email du user
     * @param password le mot de passe du user
     * @return TRUE sien cas de création avec succès du customer, FALSE sinon
     * @return NULL s'il y a une exception déclenchée 
     */
    function createUser($email, $password)
    {
        $sql = "INSERT INTO users (email, password) VALUES (:email, :password)";
        try {
            $result = $this->connexion->prepare($sql);
            $var = $result->execute(array(
                ':email' => $email,
                ':password' => sha1($password)
            ));

            if ($var) {
                return TRUE;
            } else {
                return FALSE;
            }
        } catch (PDOException $th) {
            return NULL;
        }
    }

    /**
     * fonction qui sert à récupérer les events au sein de la base de données un paramètre et sa valeur OK
     * @param item_type le paramètre
     * @param item_value lla valeur du paramètre
     * @return array tableau contenant les events, en cas de succès, FALSE sinon
     * @return NULL s'il y a une exception déclenchée 
     */
    function getSearchEvent($item_type, $item_value)
    {
        try {

            $sql = 'SELECT * FROM event WHERE ' . $item_type . ' REGEXP \'' . $item_value . '\'';

            $result = $this->connexion->prepare($sql);
            $var = $result->execute();
            $data_events = $result->fetchAll(PDO::FETCH_ASSOC);

            if ($data_events) {
                return $data_events;
            } else {
                return FALSE;
            }
        } catch (PDOException $th) {
            return NULL;
        }
    }
}
