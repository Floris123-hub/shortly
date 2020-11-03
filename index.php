<?php
    if (isset($_GET['q'])){
        $shortcut = htmlspecialchars($_GET['q']);

        $bdd = new PDO('mysql:host=localhost;dbname=shortly;charset=utf8;', 'root', '');

        $req = $bdd->prepare('SELECT COUNT(*) AS x FROM links where url=?');

        $req->execute(array($shortcut));

        while ($result = $req->fetch()){

            if ($result['x'] !=1){
                header('location: ?error=true&message=Adresse url non connue ');
                exit();
            }

        }

        $req = $bdd->prepare('SELECT * FROM links where shortcut=?');

        $req->execute(array($shortcut));

        while ($result = $req->fetch()){
            header('location: '.$result['url']);
            exit();
        }
    }

    if (isset($_POST['url'])){
        $url = $_POST['url'];

        if (!filter_var($url, FILTER_VALIDATE_URL)){
            header('location: ?error=true&message=Adresse url non valide');
            exit();
        }

        $shortcut = crypt($url, time());

        $bdd = new PDO('mysql:host=localhost;dbname=shortly;charset=utf8;', 'root', '');

        $req = $bdd->prepare('SELECT COUNT(*) AS x FROM links where url=?');

        $req->execute(array($url));

        while ($result = $req->fetch()){

            if ($result['x'] !=0){
                header('location: ?error=true&message=Adresse déjà raccourcie ');
                exit();
            }

        }

        $req = $bdd->prepare('INSERT INTO links(url, shortcut) VALUES (?, ?)');

        $req->execute(array($url, $shortcut));

        header('location: ?short='.$shortcut);
        exit();
    }
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>shortly</title>
        <link rel="stylesheet" href="design/default.css">
        <link rel="icon" href="pictures/favico.png">
    </head>
    <body>
        <section id="hello">
            <div class="container">
                <header>
                    <img src="pictures/logo.png" alt="logo" id="logo">
                </header>
                <h1>Raccourcissez vos liens en un clic !</h1>
                <h2>Rapide et efficace, en plus c'est gratuit !</h2>
                <form method="post" action="index.php">
                    <input type="url" name="url" placeholder="Collez votre lien ici">
                    <input type="submit" value="Raccourcir">
                </form>

                <?php
                    if (isset($_GET['error']) && isset($_GET['message'])){ ?>
                        <div class="center">
                            <div id="result">
                                <b style="color: #f34545"> <?php echo htmlspecialchars($_GET['message']);?> ! </b>
                            </div>
                        </div>
                    <?php } elseif (isset($_GET['short'])){ ?>
                        <div class="center">
                            <div id="result">
                                <b> URL : </b>
                                http://localhost/?q=<?php echo htmlspecialchars($_GET['short']);?>
                            </div>
                        </div>
                    <?php } ?>
                ?>
            </div>
        </section>

        <section id="brands">
            <div class="container">
                <h3>Nos sponsors</h3>
                <img src="pictures/1.png" alt="sponsor1" class="picture">
                <img src="pictures/2.png" alt="sponsor2" class="picture">
                <img src="pictures/3.png" alt="sponsor3" class="picture">
                <img src="pictures/4.png" alt="sponsor4" class="picture">
            </div>
        </section>

        <footer>
            <img src="pictures/logo2.png" alt="logo" id="logo">
            <br/>2020 &copy; Bitly
        </footer>
    </body>
</html>