<?php
function verifParams()
{
  if (isset($_POST) && sizeof($_POST) > 0) {
    foreach ($_POST as $key => $value) {
      $data = trim($value);
      $data = stripslashes($data);
      $data = strip_tags($data);
      $data = htmlspecialchars($data);
      $_POST[$key] = $data;
    }
    //print_r($_POST);exit();
  }
}

// print_r($_SERVER);
// exit();
function checkData($tab, $file_tab)
{
  global $model;
  $result_books = array();
  $data = array();

  foreach ($tab as $key => $value) {
    $value = trim($value);
    $data[$key] = $value;

    //GENERAL
    if ($value === "") {
      $result_books[$key] = "Le champs " . $key . "  ne peut pas être vide";
    }

    //SPECIFIQUE
    if ($key == "nameEvent" && !isset($result_books[$key])) {
      if (strlen($value) < 2) {
        $result_books[$key] = "Le nom doit faire au moins 2 caratères !";
      }
      if (strlen($value) > 20) {
        $result_books[$key] = "Le nom doit faire au maxium 20 caratères !";
      }
    }

    if ($key == "lieu" && !isset($result_books[$key])) {
      if (strlen($value) < 5) {
        $result_books[$key] = "Le lieu doit faire au moins 5 caratères !";
      }
      if (strlen($value) > 20) {
        $result_books[$key] = "Le lieu doit faire au plus 20 caratères  !";
      }
    }

    if ($key == "eventDate" && !isset($result_books[$key])) {
      $dateJour =  date("Y-n-j");
      // exit();
      if (strtotime($value) < strtotime($dateJour)) {
        $result_books[$key] = "La date doit être supérieur ou égale à ce jour !";
      }
    }

    if ($key == "description" && !isset($result_books[$key])) {
      if (strlen($value) > 100) {
        $result_books[$key] = "le resume doit compter moins de 100 caractères !";
      }
    }
  }
  /*  print_r($data);
  echo '<br><br><br>';
  print_r($result_books);
  echo '<br><br><br>';
  print_r($file_tab);
  exit();*/
  //traitement sur le fichier

  $file_name = $file_tab["image"]["name"];
  $table_explode = explode('.', $file_name);
  $data["fileExtension"] = $table_explode[count($table_explode) - 1];

  $data["file_size"] = $file_tab["image"]["size"];
  $data["file_error"] = $file_tab["image"]["error"];
  $data["file_tmp"] = $file_tab["image"]["tmp_name"];


  if (isset($file_tab["image"]["name"]) && !isset($result_books["name"])) {
    if ($file_tab["image"]["name"] === "") {
      $result_books["name"] = "veullez choisir une image d'illustration !";
    } else {
      $extension_autorise = ["jpeg", "png", "jpg"];
      if (!in_array($data["fileExtension"], $extension_autorise)) {
        $result_books["name"] = "extension d'images autorisées : jpeg, jpg, png !";
      }
    }
  }

  if (isset($file_tab["image"]["size"]) && !isset($result_books["size"])) {
    if ($data["file_size"] > 5000000) {
      $result_books["size"] = "taille maximal autorisée 5Mo !";
    }
  }

  // print_r($result_books);
  // exit();
  //rename file and save it if not error
  if (empty($result_books)) {
    $message_name = "";
    $code = "az12345678MWXC9ertyuiUIOPQSDFGHJopqsdfgh123456789jklmwxcvbn123456789AZERTYKLVBN";

    $index = 1;
    while ($index <= 20) {
      $message_name .= $code[rand(0, 78)];
      $index++;
    }

    $data["file_rename"] = $message_name;


    //copie du fichier sur serveur
    $file_fullname = $data["file_rename"] . '.' . $data["fileExtension"];
    $file_folder = "images" . SP . "books" . SP . $data["file_rename"] . '.' . $data["fileExtension"];
    if ($data["file_error"] == 0) {
      $result_copy = copy($data["file_tmp"], $file_folder);
    }

    //enregistrement des donnees en bdd
    $createBooksResult = $model->createEvents($_POST["nameEvent"], $_POST["lieu"], $_POST["eventDate"], $_POST["heure"], $_POST["description"], $_SESSION["user"]["id"], $file_fullname);
    // echo $createBooksResult;
    // print_r($data);
    // exit();

    if ($createBooksResult) {
      echo '
      <div class="d-grid gap-2">
        <button class="btn btn-success" type="button">Event enregistré avec succès</button>
      </div>
      ';
    } else {
      echo '
      <div class="d-grid gap-2">
        <button class="btn btn-danger" type="button">Echec de l\'enregistrement !</button>
      </div>
      ';
    }
  } else {
    $createBooksResult = NULL;
  }

  return [$result_books, $data, $createBooksResult];
}
function updatData($tab)
{
  global $model;
  $result_books = array();
  $data = array();

  foreach ($tab as $key => $value) {
    $value = trim($value);
    $data[$key] = $value;

    //GENERAL
    if ($value === "") {
      $result_books[$key] = "Le champs " . $key . "  ne peut pas être vide";
    }

    //SPECIFIQUE
    if ($key == "nameEvent" && !isset($result_books[$key])) {
      if (strlen($value) < 2) {
        $result_books[$key] = "Le nom doit faire au moins 2 caratères !";
      }
      if (strlen($value) > 20) {
        $result_books[$key] = "Le nom doit faire au maxium 20 caratères !";
      }
    }

    if ($key == "lieu" && !isset($result_books[$key])) {
      if (strlen($value) < 5) {
        $result_books[$key] = "Le lieu doit faire au moins 5 caratères !";
      }
      if (strlen($value) > 20) {
        $result_books[$key] = "Le lieu doit faire au plus 20 caratères  !";
      }
    }

    if ($key == "eventDate" && !isset($result_books[$key])) {
      $dateJour =  date("Y-n-j");
      // exit();
      if (strtotime($value) < strtotime($dateJour)) {
        $result_books[$key] = "La date doit être supérieur ou égale à ce jour !";
      }
    }

    if ($key == "description" && !isset($result_books[$key])) {
      if (strlen($value) > 100) {
        $result_books[$key] = "le resume doit compter moins de 100 caractères !";
      }
    }
  }

  if (empty($result_books)) {

    //mise a jour des donnees en bdd
    $createBooksResult = $model->updatEvents($_POST["nameEvent"], $_POST["lieu"], $_POST["eventDate"], $_POST["heure"], $_POST["description"], $_SESSION["user"]["id"], $_POST["event_id"], $_SESSION["user"]["image"]);
    if ($createBooksResult) {
      echo '
      <div class="d-grid gap-2">
        <button class="btn btn-success" type="button">Event updat avec succès</button>
      </div>
      ';
    } else {
      echo '
      <div class="d-grid gap-2">
        <button class="btn btn-danger" type="button">Echec de la mise à jour !</button>
      </div>
      ';
    }
  } else {
    $createBooksResult = NULL;
  }

  return [$result_books, $data, $createBooksResult];
}

function displayConnexion()
{
  global $members;
  global $events;
  global $booking;
  // global $gender;
  $result = '
  <div class="row">
  <div class="col-9 border border-end-primary d-flex flex-wrap">';
  if ($events) {

    foreach ($events as $key => $value) {
      // echo SRC . SP . 'images' . SP . 'books' . SP . $value["image"];
      // exit();
      $result .= '
  <div class="card m-3" style="width: 18rem;">
  <img src="images' . SP . 'books' . SP . $value["image"] . '" class="card-img-top" alt="illustration">
  <div class="card-body">
    <h5 class="card-title"><strong>Title : </strong>' . $value["name"] . '</h5>
     <p class="card-text"><strong>Resume : </strong>
     ' . $value["description"] . '
     </p>
  </div>
  <ul class="list-group list-group-flush">
    <li class="list-group-item"><strong>Lieu : </strong>' . $value["lieu"] . '</li>
    <li class="list-group-item"><strong>Date : </strong>' . $value["date"] . '</li>
    <li class="list-group-item"><strong>Heure : </strong>' . $value["heure"] . '</li>
    <li class="list-group-item"><strong>Promoteur : </strong>';
      if ($members) {
        foreach ($members as $keyM => $valueM) {
          if ($valueM["id"] == $value["owner_id"]) {
            $result .= $valueM['email'] . '</td>';
          }
        }
      }
      $result .= '</li>
  </ul>
  <div class="card-body">
  ';
      if (isset($_SESSION["user"])) {
        $id_to_show = $_SESSION["user"]["id"];
      } else {
        $id_to_show = "";
      }
      $button_at_end = '
        <form action="askbooking" method="POST">
          <input type="hidden" name="id_user_booking" value="' . $id_to_show . '"/>
          <input type="hidden" name="event_booking_id" value="' . $value["id"] . '"/>
          <input type="submit" name="ask_booking" value="Reserver"/>
        </form>
        ';
      if ($booking) {
        if (isset($_SESSION["user"])) {
          // print_r($booking);
          // exit();
          for ($i = 0; $i < count($booking); $i++) {
            if (($booking[$i]["user_id"] == $_SESSION["user"]["id"]) && ($booking[$i]["event_id"] == $value["id"]) && ($booking[$i]["status"] == 1)) {
              //reservation faite => on ecrase la precedente
              $button_at_end = '
<button type="button" class="btn btn-warning btn-sm">
    Déjà reservé
</button>
';
            }
            if (($booking[$i]["user_id"] == $_SESSION["user"]["id"]) && ($booking[$i]["event_id"] == $value["id"]) && ($booking[$i]["status"] == 0)) {
              //en cours de traitement ==> on ecrase la valeur precedante
              $button_at_end = '
<button type="button" class="btn btn-primary btn-sm">
    Reservation en attente
</button>
';
            }
          }
        }
      }
      $result .= $button_at_end;
      $result .= '
</div>
</div>';
    }
  }
  $result .= '</div>
<div class="col-3">
    <div class="books_search_box"></div>
</div>
</div>
';
  return $result;
}

function displayAskbooking()
{
  global $model;
  $result = '';

  //redirection : protection
  if (!$_SESSION["user"]) {
    header('Location: ' . BASE_URL . SP . 'connexion');
  }

  if (isset($_POST["ask_booking"])) {
    // print_r($_POST);
    // exit();
    $var = $model->createBooking($_POST["event_booking_id"], $_POST["id_user_booking"]);

    if ($var) {
      $result .= '
<div class="d-grid gap-2">
    <button class="btn btn-success" type="button">
        votre reservation à été prise en compte
    </button>
</div>';
    } else {
      $result .= '
<div class="d-grid gap-2">
    <button class="btn btn-danger" type="button">
        echec de la demande ! veuillez rééssèyer plus tard !
    </button>
</div>';
    }
  }
  $result .= displayConnexion();
  return $result;
}

function displayManagevents()
{
  global $events;
  global $error_and_data;
  global $members;

  //redirection : protection
  if (!$_SESSION["user"]) {
    header('Location: ' . BASE_URL . SP . 'connexion');
  }

  //traitement des donnees denregistrement de book
  if (isset($_POST["submit_managevents"]) && !empty($_FILES)) {
    $error_and_data = checkData($_POST, $_FILES);
  }

  //error
  $error_name = isset($error_and_data[0]["nameEvent"]) && !empty($error_and_data[0]["nameEvent"]) ? $error_and_data[0]["nameEvent"] :
    null;
  $error_lieu = isset($error_and_data[0]["lieu"]) && !empty($error_and_data[0]["lieu"]) ?
    $error_and_data[0]["lieu"] : null;
  $error_date = isset($error_and_data[0]["eventDate"]) && !empty($error_and_data[0]["eventDate"]) ? $error_and_data[0]["eventDate"] :
    null;
  $error_heure = isset($error_and_data[0]["heure"]) && !empty($error_and_data[0]["heure"]) ?
    $error_and_data[0]["heure"] : null;
  $error_description = isset($error_and_data[0]["description"]) && !empty($error_and_data[0]["description"]) ? $error_and_data[0]["description"] :
    null;
  $error_resume = isset($error_and_data[0]["resume"]) && !empty($error_and_data[0]["resume"]) ?
    $error_and_data[0]["resume"] : null;
  $error_publication_date = isset($error_and_data[0]["publication_date"]) &&
    !empty($error_and_data[0]["publication_date"]) ? $error_and_data[0]["publication_date"] : null;
  $error_edition = isset($error_and_data[0]["edition"]) && !empty($error_and_data[0]["edition"]) ?
    $error_and_data[0]["edition"] : null;
  $error_image_name = isset($error_and_data[0]["name"]) && !empty($error_and_data[0]["name"]) ? $error_and_data[0]["name"]
    : null;
  $error_image_size = isset($error_and_data[0]["size"]) && !empty($error_and_data[0]["size"]) ? $error_and_data[0]["size"]
    : null;

  // print_r($error_image_size);
  // print_r($error_image_name);
  // exit();
  //value
  $value_name = isset($error_and_data[1]["nameEvent"]) && !empty($error_and_data[1]["nameEvent"]) && empty($error_and_data[2]) ?
    $error_and_data[1]["nameEvent"] : null;
  $value_lieu = isset($error_and_data[1]["lieu"]) && !empty($error_and_data[1]["lieu"]) && empty($error_and_data[2])
    ? $error_and_data[1]["lieu"] : null;
  $value_date = isset($error_and_data[1]["eventDate"]) && !empty($error_and_data[1]["eventDate"]) && empty($error_and_data[2]) ?
    $error_and_data[1]["eventDate"] : null;
  $value_heure = isset($error_and_data[1]["heure"]) && !empty($error_and_data[1]["heure"]) && empty($error_and_data[2]) ?
    $error_and_data[1]["heure"] : null;
  $value_description = isset($error_and_data[1]["description"]) && !empty($error_and_data[1]["description"]) && empty($error_and_data[2]) ?
    $error_and_data[1]["description"] : null;
  $value_resume = isset($error_and_data[1]["resume"]) && !empty($error_and_data[1]["resume"]) && empty($error_and_data[2])
    ? $error_and_data[1]["resume"] : null;
  $value_publication_date = isset($error_and_data[1]["publication_date"]) &&
    !empty($error_and_data[1]["publication_date"]) && empty($error_and_data[2]) ? $error_and_data[1]["publication_date"] :
    null;
  // $value_edition = isset($error_and_data[1]["edition"]) && !empty($error_and_data[1]["edition"]) ? $error_and_data[1]["edition"] : null;


  $result = '
<div class="row mt-5">
    <div class="col-5 here-col-5">
        <div class="addbooks">add event</div>
        <form method="POST" action="managevents" enctype="multipart/form-data">
            <div class="input-group mb-2">
                <span class="input-group-text" id="inputGroup-sizing-default">Name</span>
                <input type="text" name="nameEvent" value="' . $value_name . '" class="form-control"
                    aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
            </div>
            <div class="error">' . $error_name . '</div>
            <div class="input-group mb-2">
                <span class="input-group-text" id="inputGroup-sizing-default">Lieu</span>
                <input type="text" name="lieu" value="' . $value_lieu . '" class="form-control"
                    aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
            </div>
            <div class="error">' . $error_lieu . '</div>
            <div class="input-group mb-2">
                <span class="input-group-text" id="inputGroup-sizing-default">Date</span>
                <input type="date" name="eventDate" value="' . $value_date . '" class="form-control"
                    aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
            </div>
            <div class="error">' . $error_date . '</div>
            <div class="input-group mb-2">
                <span class="input-group-text" id="inputGroup-sizing-default">heure</span>
                <input type="time" name="heure" value="' . $value_heure . '" class="form-control"
                    aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
            </div>
            <div class="error">' . $error_heure . '</div>
            <div class="input-group mb-2">
                <span class="input-group-text" id="inputGroup-sizing-default">Description</span>
                <input type="text" name="description" value="' . $value_description . '" class="form-control"
                    aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
            </div>
            <div class="error">' . $error_description . '</div>
            <div class="input-group mb-2">
                <span class="input-group-text" id="inputGroup-sizing-default">Image</span>
                <input type="file" name="image" class="form-control" aria-label="Sizing example input"
                    aria-describedby="inputGroup-sizing-default">
            </div>
            <div class="error">' . $error_image_name . '<br>' . $error_image_size . '</div>
            <div class="input-group mb-2">
                <input type="submit" name="submit_managevents" class="form-control" aria-label="Sizing example input"
                    aria-describedby="inputGroup-sizing-default">
            </div>
        </form>
    </div>
    <div class="col-7">
        <div class="addbooks">events list</div>
        <div class="table_content">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Lieu</th>
                        <th scope="col">Date</th>
                        <th scope="col">Heure</th>
                        <th scope="col">Promoteur</th>
                        <th scope="col"></th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    ';
  if ($events) {
    foreach ($events as $key => $value) {
      $result .= '
                    <tr>
                        <th scope="row" class="cle">' . $key + 1 . '</th>
                        <td scope="col" class="name">' . $value['name'] . '</td>
                        <td scope="col" class="lieu">' . $value['lieu'] . '</td>
                        <td scope="col" class="date">' . $value['date'] . '</td>
                        <td scope="col" class="heure">' . $value['heure'] . '</td>
                        <td scope="col" class="owner_id">';
      if ($members) {
        foreach ($members as $keyM => $valueM) {
          if ($valueM["id"] == $value["owner_id"]) {
            $result .= $valueM['email'] . '</td>';
          }
        }
      }

      if ($value["owner_id"] == $_SESSION["user"]["id"]) {
        $result .= '    <td scope="col">
                        <button class="update view anta-regular">
                                <form method="POST" action="updatevents">
                                    <input type="hidden" name="event_id_to_updat" value="' . $value['id'] . '" />
                                    <input type="submit" value="Updt" />
                                </form>
                            </button>
                        </td>
                        <td scope="col">
                            <button class="delete view anta-regular">
                                <form method="POST" action="deleteevents">
                                    <input type="hidden" name="event_id_to_delete" value="' . $value['id'] . '" />
                                    <input type="submit" value="Delt" />
                                </form>
                            </button>
                        </td>
                        <td scope="col"></td>
                    </tr>
                    ';
      }
    }
  }
  $result .= '
                </tbody>
                <tfoot>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Lieu</th>
                        <th scope="col">Date</th>
                        <th scope="col">Heure</th>
                        <th scope="col">Promoteur</th>
                        <th scope="col"></th>
                        <th scope="col"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
';

  return $result;
}

function displayDeleteevents()
{
  global $model;
  global $members;

  $result = "";

  //redirection : protection
  if (!$_SESSION["user"]) {
    header('Location: ' . BASE_URL . SP . 'connexion');
  }

  if (isset($_POST["event_id_to_delete_ok"]) && !empty($_POST["event_id_to_delete_ok"])) {
    // print_r($_POST);
    // exit();

    $deleteStatus = $model->deleteEventById($_POST["event_id_to_delete_ok"]);
    // print_r($deleteStatus);
    // exit();

    if ($deleteStatus) {
      unlink(SRC . SP . 'images' . SP . 'books' . SP . $_POST["event_id_to_delete_image"]);
      $result .= '
<div class="d-grid gap-2">
    <button class="btn btn-success" type="button">
        le projet a été suprimé avec succès
    </button>
</div>';
      $result .= displayManagevents();
      return $result;
    } else {
      $result .= '
<div class="d-grid gap-2">
    <button class="btn btn-danger" type="button">
        Echec de suppression du projet
    </button>
</div>';
      $result .= displayManagevents();
      return $result;
    }
  }

  $eventById = $model->getEventById($_POST["event_id_to_delete"]);
  // print_r($eventById);
  // exit();
  if (!empty($eventById) && isset($_POST["event_id_to_delete"]) && !empty($_POST["event_id_to_delete"])) {
    foreach ($eventById as $key => $value) {
      $result .= '<div class="card" style="width: 18rem;">
    <img src="' . BASE_URL . SP . 'images' . SP . 'books' . SP . $value["image"] . '" class="card-img-top" alt="image livre">
  <div class="card-body">
    <h5 class="card-title"><strong>' . $value["name"] . '</strong></h5>
    <p class="card-text"><em>' . $value["description"] . '</em></p>
  </div>
  <ul class="list-group list-group-flush">
    <li class="list-group-item"><strong>Lieu : </strong>' . $value["lieu"] . '</li>
    <li class="list-group-item"><strong>Date : </strong>' . $value["date"] . '</li>
    <li class="list-group-item"><strong>Heure : </strong>' . $value["heure"] . '</li>
    <li class="list-group-item"><strong>Promoteur : </strong>';
      if ($members) {
        foreach ($members as $keyM => $valueM) {
          if ($valueM["id"] == $value["owner_id"]) {
            $result .= $valueM['email'] . '</td>';
          }
        }
      }
      $result .= '</li>
  </ul>
  <div class="card-body">
    <button type="button" class="btn btn-primary">
    <form action="deleteevents" method="POST">
    <input type="hidden" name="event_id_to_delete_ok" value="' . $value["id"] . '"/>
    <input type="hidden" name="event_id_to_delete_image" value="' . $value["image"] . '"/>
    <input type="submit" value="delete event" name="delete_this_id"/>
    </form>
    </button>
  </div>
</div>';
    }
    return $result;
  } else {
    $result .= '
    <div class="d-grid gap-2">
    <button class="btn btn-primary" type="button">
     le projet n\'est pas dans la base de donnee </button>
</div>';

    $result .= displayManagevents();
    return $result;
  }
}


function displayUpdatevents()
{
  global $model;
  global $error_and_data;

  //redirection : protection
  if (!$_SESSION["user"]) {
    header('Location: ' . BASE_URL . SP . 'connexion');
  }

  if (isset($_POST["event_id_to_updat"]) && !empty($_POST["event_id_to_updat"])) {
    $event_to_updat = $model->getEventById($_POST["event_id_to_updat"]);

    $event_to_updat[0]["nameEvent"] = $event_to_updat[0]["name"];
    unset($event_to_updat[0]["name"]);

    $event_to_updat[0]["eventDate"] = $event_to_updat[0]["date"];
    unset($event_to_updat[0]["date"]);

    $_SESSION["user"]["image"] = $event_to_updat[0]["image"];
  }

  //traitement des donnees denregistrement de event

  if (isset($_POST["submit_updatevent"])) {
    foreach ($_POST as $key => $value) {
      $event_to_updat[0][$key] = $value;
      if ($key == "name") {
        unset($event_to_updat[0][$key]);
        $event_to_updat[0]["nameEvent"] = $value;
      }
      if ($key == "date") {
        unset($event_to_updat[0][$key]);
        $event_to_updat[0]["eventDate"] = $value;
      }
    }
    $error_and_data = updatData($_POST);
  }

  // print_r($event_to_updat);
  // exit();

  //error
  $error_name = isset($error_and_data[0]["nameEvent"]) && !empty($error_and_data[0]["nameEvent"]) ? $error_and_data[0]["nameEvent"] :
    null;
  $error_lieu = isset($error_and_data[0]["lieu"]) && !empty($error_and_data[0]["lieu"]) ?
    $error_and_data[0]["lieu"] : null;
  $error_date = isset($error_and_data[0]["eventDate"]) && !empty($error_and_data[0]["eventDate"]) ? $error_and_data[0]["eventDate"] :
    null;
  $error_heure = isset($error_and_data[0]["heure"]) && !empty($error_and_data[0]["heure"]) ?
    $error_and_data[0]["heure"] : null;
  $error_description = isset($error_and_data[0]["description"]) && !empty($error_and_data[0]["description"]) ?
    $error_and_data[0]["description"] : null;

  //value
  $value_name = isset($error_and_data[1]["nameEvent"]) && !empty($error_and_data[1]["nameEvent"]) && empty($error_and_data[2]) ?
    $error_and_data[1]["nameEvent"] : $event_to_updat[0]["nameEvent"];
  $value_lieu = isset($error_and_data[1]["lieu"]) && !empty($error_and_data[1]["lieu"]) && empty($error_and_data[2])
    ? $error_and_data[1]["lieu"] : $event_to_updat[0]["lieu"];
  $value_date = isset($error_and_data[1]["eventDate"]) && !empty($error_and_data[1]["eventDate"]) && empty($error_and_data[2]) ? $_POST["eventDate"] : $event_to_updat[0]["eventDate"];
  // $value_gender = isset($error_and_data[1]["gender"]) && !empty($error_and_data[1]["gender"]) ? $error_and_data[1]["gender"] : null;
  $value_heure = isset($error_and_data[1]["heure"]) && !empty($error_and_data[1]["heure"]) && empty($error_and_data[2]) ?
    $error_and_data[1]["heure"] : $event_to_updat[0]["heure"];
  $value_description = isset($error_and_data[1]["description"]) && !empty($error_and_data[1]["description"]) && empty($error_and_data[2])
    ? $error_and_data[1]["description"] : $event_to_updat[0]["description"];
  $value_event_id = isset($_POST["event_id_to_updat"]) && !empty($_POST["event_id_to_updat"]) ? $_POST["event_id_to_updat"] : $event_to_updat[0]["event_id"];
  $value_image = isset($_POST["event_id_to_updat"]) && !empty($_POST["event_id_to_updat"]) ? $event_to_updat[0]["image"] : $_SESSION["user"]["image"];

  // $value_edition = isset($error_and_data[1]["edition"]) && !empty($error_and_data[1]["edition"]) ? $error_and_data[1]["edition"] : null;


  $result = '
<div class="row mt-5">
    <div class="col-5 here-col-5">
        <div class="addbooks">updat event</div>
        <form method="POST" action="updatevents" enctype="multipart/form-data">
            <div class="input-group mb-2">
                <span class="input-group-text" id="inputGroup-sizing-default">Name</span>
                <input type="text" name="nameEvent" value="' . $value_name . '" class="form-control"
                    aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
            </div>
            <div class="error">' . $error_name . '</div>
            <div class="input-group mb-2">
                <span class="input-group-text" id="inputGroup-sizing-default">Lieu</span>
                <input type="text" name="lieu" value="' . $value_lieu . '" class="form-control"
                    aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
            </div>
            <div class="error">' . $error_lieu . '</div>
            <div class="input-group mb-2">
                <span class="input-group-text" id="inputGroup-sizing-default">Date</span>
                <input type="date" name="eventDate" value="' . $value_date . '" class="form-control"
                    aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
            </div>
            <div class="error">' . $error_date . '</div>
            <div class="input-group mb-2">
                <span class="input-group-text" id="inputGroup-sizing-default">Heure</span>
                <input type="time" name="heure" value="' . $value_heure . '" class="form-control"
                    aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
            </div>
            <div class="error">' . $error_heure . '</div>
            <div class="input-group mb-2">
                <span class="input-group-text" id="inputGroup-sizing-default">Description</span>
                <input type="text" name="description" value="' . $value_description . '" class="form-control"
                    aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
            </div>
            <div class="error">' . $error_description . '</div>
            <div class="input-group mb-2">
                <input type="hidden" name="event_id" value="' . $value_event_id . '" class="form-control" aria-label="Sizing example input"
                    aria-describedby="inputGroup-sizing-default">
            </div>
            <div class="input-group mb-2">
                <input type="submit" name="submit_updatevent" class="form-control" aria-label="Sizing example input"
                    aria-describedby="inputGroup-sizing-default">
            </div>
        </form>
    </div>
    <div class="col-7">
        <div class="addbooks">Event image</div>
        <img src="images' . SP . 'books' . SP . $value_image . '"  class="card-img-top updt_img" alt="illustration">
    </div>
</div>
</div>
';

  return $result;
}

function displayBooking()
{
  global $model;
  global $booking;
  global $members;
  global $events;
  // global $book_user_stats;

  $result = '';

  //redirection : protection
  if (!$_SESSION["user"]) {
    header('Location: ' . BASE_URL . SP . 'connexion');
  }

  //approval booking
  if (isset($_POST["validate_booking"])) {
    $var_approval = $model->updatBookingAskingList($_POST["booking_id_to_approv"]);
    if ($var_approval) {
      $result .= '
<div class="d-grid gap-2">
    <button class="btn btn-success" type="button">
        la à été validée avec succès
    </button>
</div>';
    } else {
      $result .= '
<div class="d-grid gap-2">
    <button class="btn btn-danger" type="button">
        echec de validation de la demande
    </button>
</div>';
    }
  }

  $result .= '
<div class="row">
    <div class="col-12">
        <div class="addbooks">gerer les reservations</div>
        <div class="table_content">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">event name</th>
                        <th scope="col">Lieu</th>
                        <th scope="col">Date</th>
                        <th scope="col">Heure</th>
                        <th scope="col">demandeur</th>
                        <th scope="col">status</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    ';
  if ($booking) {
    foreach ($booking as $key => $value) {
      if ($events) {
        foreach ($events as $keyM => $valueM) {
          if ($valueM["id"] == $value["event_id"]) {
            $dateJour =  date("Y-n-j");
            if (strtotime($valueM["date"]) > strtotime($dateJour)) {
              $result .= '
                    <tr>
                        <th scope="row" class="cle">' . $key + 1 . '</th>
                        <td scope="col" class="event_name">' . $valueM['name'] . '</td>
                        <td scope="col" class="lieu">' . $valueM['lieu'] . '</td>
                        <td scope="col" class="date">' . $valueM['date'] . '</td>
                        <td scope="col" class="heure">' . $valueM['heure'] . '</td>
                        <td scope="col" class="demandeur">';
              if ($members) {
                foreach ($members as $keyP => $valueP) {
                  if ($valueP["id"] == $value["user_id"]) {
                    $result .= $valueP['email'] . '</td>';
                  }
                }
              }
              $result .= '</td>
                        <td scope="col" class="status">' . $value["status"] . '</td>
                        <td scope="col">';
              if ($value['status'] == 0 && $valueM["owner_id"] == $_SESSION["user"]["id"]) {
                $result .= '
                            <button class="approval_borrow view anta-regular btn-success">
                                <form method="POST" action="booking">
                                    <input type="hidden" name="booking_id_to_approv"
                                        value="' . $value['id'] . '" />
                                    <input type="submit" name="validate_booking" value="Valid" />
                                </form>
                            </button>
                        </td>
                    </tr>
                    ';
              }
            }
          }
        }
      }
    }
  }
  $result .= '
                </tbody>
                <tfoot>
                    <tr>
                       <th scope="col">#</th>
                        <th scope="col">event name</th>
                        <th scope="col">Lieu</th>
                        <th scope="col">Date</th>
                        <th scope="col">Heure</th>
                        <th scope="col">demandeur</th>
                        <th scope="col">status</th>
                        <th scope="col"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
</div>
';

  return $result;
}

function displayAuthentification()
{
  // $mdp = "adminLibrary";
  // $mdp = sha1($mdp);
  // echo $mdp;
  //on charges les donnees du user
  global $model;
  //redirection : protection
  if (!isset($_POST["email"]) || !isset($_POST["password"])) {
    header('Location: ' . BASE_URL . SP . 'connexion');
  }

  $authentData = $model->authentifier($_POST["email"], $_POST["password"]);
  // print_r($authentData);
  // exit();

  $result = '';
  if (!$authentData) {
    $result .= '
    <div class="d-grid gap-2">
      <button class="btn btn-danger" type="button">Echec de connexion, veullez reessaye plus tard !</button>
    </div>
    ';
    $result .= displayConnexion();
    return $result;
  } else {
    $_SESSION["user"] = [];
    foreach ($authentData as $key => $value) {
      $_SESSION["user"][$key] = $value;
    }

    $result .= displayConnexion();
    return $result;
  }
}

function displayDeconnexion()
{
  session_destroy();

  header('Location: ' . BASE_URL . SP . 'connexion');
}

function displayProfil()
{
  global $events;
  global $booking;
  global $members;

  //redirection : protection
  if (!$_SESSION["user"]) {
    header('Location: ' . BASE_URL . SP . 'connexion');
  }

  $result = '
  <div class="row">
    <div class="col-5">
     <div class="addbooks">my profil</div>
      <form method="" action="#" id="user_data">
            <div class="input-group mb-2">
                <span class="input-group-text" id="inputGroup-sizing-default">User Email</span>
                <input type="email" name="user_level" value="' . $_SESSION["user"]["email"] . '" class="form-control"
                    aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" disabled>
            </div>
            <div class="input-group mb-2">
                <span class="input-group-text" id="inputGroup-sizing-default">User Name</span>
                <input type="text" name="user_fname" value="' . $_SESSION["user"]["f_name"] . '" class="form-control"
                    aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" disabled>
            </div>
            <div class="input-group mb-2">
                <span class="input-group-text" id="inputGroup-sizing-default">User Lastname</span>
                <input type="text" name="user_lname" value="' . $_SESSION["user"]["l_name"] . '" class="form-control"
                    aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" disabled>
            </div>
            <div class="input-group mb-2">
                <span class="input-group-text" id="inputGroup-sizing-default">User Age</span>
                <input type="number" name="user_age" value="' . $_SESSION["user"]["age"] . '" class="form-control"
                    aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" disabled>
            </div>
            <div class="input-group mb-2">
                <span class="input-group-text" id="inputGroup-sizing-default">User Sexe</span>
                <input type="text" name="user_sexe" value="' . $_SESSION["user"]["sexe"] . '" class="form-control"
                    aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" disabled>
            </div>
            <div class="input-group mb-2">
                <span class="input-group-text" id="inputGroup-sizing-default">User ID</span>
                <input type="number" name="user_id" value="' . $_SESSION["user"]["id"] . '" class="form-control"
                    aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" disabled>
            </div>
            <div class="input-group mb-2">
                <span class="input-group-text" id="inputGroup-sizing-default">Description</span>
                <input type="text" name="user_description" value="' . $_SESSION["user"]["description"] . '" class="form-control"
                    aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" disabled>
            </div>
            <div class="input-group mb-2">
                <input type="submit" class="form-control" aria-label="Sizing example input"
                    aria-describedby="inputGroup-sizing-default" id="bt_updat_user" value="Updat Profil">
            </div>
      </form>
    </div>
    <div class="col-7">
      <div class="addbooks">my reserved events</div>
      <div class="d-flex flex-wrap justify-content-center">
      ';
  if ($events) {
    foreach ($events as $key => $value) {
      if ($booking) {
        for ($i = 0; $i < count($booking); $i++) {
          if ($booking[$i]["user_id"] == $_SESSION["user"]["id"] && $booking[$i]["status"] == 1 && $booking[$i]["event_id"] == $value["id"]) {
            $dateJour =  date("Y-n-j");
            if (strtotime($value["date"]) > strtotime($dateJour)) {
              $result .= '
  <div class="card m-3" style="width: 18rem;">
  <img src="images' . SP . 'books' . SP . $value["image"] . '" class="card-img-top" alt="illustration">
  <div class="card-body">
    <h5 class="card-title"><strong>Name : </strong>' . $value["name"] . '</h5>
     <p class="card-text"><strong>Description : </strong>
     ' . $value["description"] . '
     </p>
  </div>
  <ul class="list-group list-group-flush">
    <li class="list-group-item"><strong>Lieu : </strong>' . $value["lieu"] . '</li>
    <li class="list-group-item"><strong>Date : </strong>' . $value["date"] . '</li>
    <li class="list-group-item"><strong>Heure : </strong>' . $value["heure"] . '</li>
    <li class="list-group-item"><strong>Promoteur : </strong>';
              if ($members) {
                foreach ($members as $keyP => $valueP) {
                  if ($valueP["id"] == $value["owner_id"]) {
                    $result .= $valueP['email'] . '</td>';
                  }
                }
              }
              $result .= '</li>
  </ul>
  </div>
  ';
            }
          }
        }
      }
    }
  }
  $result .= '
  </div>
  </div>
  ';
  return $result;
}

function displayUpdatUser()
{
  //redirection : protection
  if (!$_SESSION["user"]) {
    header('Location: ' . BASE_URL . SP . 'connexion');
  }

  global $model;
  if (isset($_POST["updatProfilData"])) {
    $updatResult = $model->updatUser($_SESSION["user"]["email"], $_POST["user_fname"], $_POST["user_lname"], $_POST["user_age"], $_POST["user_sexe"], $_POST["user_description"]);
    $result = '';
    if ($updatResult) {
      //mise à jour des infos
      $_SESSION["user"]["f_name"] = $updatResult["f_name"];
      $_SESSION["user"]["l_name"] = $updatResult["l_name"];
      $_SESSION["user"]["age"] = $updatResult["age"];
      if ($updatResult["sexe"] == 1) {
        $_SESSION["user"]["sexe"] = "masculin";
      } else {
        $_SESSION["user"]["sexe"] = "feminin";
      }
      $_SESSION["user"]["description"] = $updatResult["description"];

      $result .= '
    <div class="d-grid gap-2">
      <button class="btn btn-success" type="button">
        profil modifié avec succes !
      </button>
    </div>
    ';
    } else {
      $result .= '
    <div class="d-grid gap-2">
      <button class="btn btn-danger" type="button">
        echec de modification !
      </button>
    </div>
    ';
    }
    $result .= displayProfil();
    return $result;
  } else {
    $result = displayProfil();
    return $result;
  }
}

function displayCreatetUser()
{
  global $model;
  //redirection : protection
  if (!isset($_POST["createUserData"])) {
    header('Location: ' . BASE_URL . SP . 'connexion');
  }
  // print_r($_POST);
  // exit();
  $result = '';
  if (isset($_POST["createUserData"])) {

    $createUserResult = $model->createUser($_POST["user_email"], $_POST["user_password"]);

    if ($createUserResult) {
      $result .= '
      <div class="d-grid gap-2">
      <button class="btn btn-success" type="button">
       Compte créé avec succes !
      </button>
    </div>
      ';
      //chargement des donnees user
      $authentData = $model->authentifier($_POST["user_email"], $_POST["user_password"]);
      $_SESSION["user"] = [];
      foreach ($authentData as $key => $value) {
        $_SESSION["user"][$key] = $value;
      }

      $result .= displayProfil();
    } else {
      $result .= '
      <div class="d-grid gap-2">
      <button class="btn btn-danger" type="button">
        erreur ! email existe !
      </button>
    </div>
      ';

      $result .= displayConnexion();
    }
  }
  // $result .= displayProfil();
  return $result;
}

function displaySearchEventRequest()
{
  global $model;
  global $booking;
  global $members;
  // global $gender;


  if (isset($_POST["searchEvent"])) {
    $events_search = $model->getSearchEvent($_POST["item_type"], $_POST["item_value"]);
    // print_r($books_search);
    // exit();
    $message_results = '
    <div class="d-grid gap-2">
      <button class="btn btn-primary" type="button">
      Results for <strong>' . $_POST["item_type"] . ' : ' . $_POST["item_value"] . '</strong>
      </button>
    </div>
    ';
    $result = '
    <div class="row">
    <div class="col-9 border border-end-primary d-flex flex-wrap">';
    if ($events_search) {

      foreach ($events_search as $key => $value) {
        $result .= '
    <div class="card m-3" style="width: 18rem;">
    <img src="images' . SP . 'books' . SP . $value["image"] . '" class="card-img-top" alt="illustration">
    <div class="card-body">
      <h5 class="card-title"><strong>Name : </strong>' . $value["name"] . '</h5>
       <p class="card-text"><strong>Description : </strong>
       ' . $value["description"] . '
       </p>
    </div>
    <ul class="list-group list-group-flush">
      <li class="list-group-item"><strong>Lieu : </strong>' . $value["lieu"] . '</li>
      <li class="list-group-item"><strong>Date : </strong>' . $value["date"] . '</li>
      <li class="list-group-item"><strong>Heure : </strong>' . $value["heure"] . '</li>
      <li class="list-group-item"><strong>Promoteur : </strong>';
        if ($members) {
          foreach ($members as $keyM => $valueM) {
            if ($valueM["id"] == $value["owner_id"]) {
              $result .= $valueM['email'] . '</td>';
            }
          }
        }
        $result .= '</li>
    </ul>
    <div class="card-body">
    ';
        if (isset($_SESSION["user"])) {
          $id_to_show = $_SESSION["user"]["id"];
        } else {
          $id_to_show = "";
        }
        $button_at_end = '
      <form action="askbooking" method="POST">
        <input type="hidden" name="id_user_booking" value="' . $id_to_show . '"/>
        <input type="hidden" name="event_booking_id" value="' . $value["id"] . '"/>
        <input type="submit" name="ask_booking" value="Reserver"/>
      </form>
      ';
        if ($booking) {
          if (isset($_SESSION["user"])) {
            // print_r($booking);
            // exit();
            for ($i = 0; $i < count($booking); $i++) {
              if (($booking[$i]["user_id"] == $_SESSION["user"]["id"]) && ($booking[$i]["event_id"] == $value["id"]) && ($booking[$i]["status"] == 1)) {
                //reservation faite => on ecrase la precedente
                $button_at_end = '
<button type="button" class="btn btn-warning btn-sm">
    Déjà reservé
</button>
';
              }
              if (($booking[$i]["user_id"] == $_SESSION["user"]["id"]) && ($booking[$i]["event_id"] == $value["id"]) && ($booking[$i]["status"] == 0)) {
                //en cours de traitement ==> on ecrase la valeur precedante
                $button_at_end = '
<button type="button" class="btn btn-primary btn-sm">
    Reservation en attente
</button>
';
              }
            }
          }
        }
        $result .= $button_at_end;
        $result .= '
  </div>
  </div>';
      }
      $result .= '</div>
  <div class="col-3">
      <div class="books_search_box"></div>
      <div class="availBorrowBookBox"></div>
  </div>
  </div>
  ';
      return $message_results . $result;
    } else {
      $message_results = '
       <div class="d-grid gap-2">
      <button class="btn btn-danger" type="button">
      AUCUN Results for <strong>' . $_POST["item_type"] . ' : ' . $_POST["item_value"] . '</strong>
      </button>
    </div>
      ';
      $result = $message_results . displayConnexion();
      return $result;
    }
  } else {

    //redirection : protection
    header('Location: ' . BASE_URL . SP . 'connexion');


    $message_results = '
       <div class="d-grid gap-2">
      <button class="btn btn-danger" type="button">
      AUCUN Results for <strong>' . $_POST["item_type"] . ' : ' . $_POST["item_value"] . '</strong>
      </button>
    </div>
      ';
    $result = $message_results . displayConnexion();
    return $result;
  }
}
