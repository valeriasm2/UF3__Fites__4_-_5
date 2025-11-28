<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>EXER5.1</title>
        <style>
            :root {
                --primary: #2563eb;
                --primary-dark: #1946a6;
                --secondary-bg: #f7f7fa;
                --input-border: #c7c7cf;
                --input-focus: #2563eb;
                --submit-bg: #2678e5;
                --submit-hover: #17428a;
                --card-bg: #fff;
                --border-radius: 12px;
            }

            body {
                background: var(--secondary-bg);
                font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                margin: 0;
                padding: 0;
                min-height: 100vh;
                display: flex;
                align-items: flex-start;
                justify-content: center;
                flex-direction: column;
            }
            form {
                width: 390px;
                margin: 55px auto 30px auto;
                background: var(--card-bg);
                padding: 32px 40px 28px 40px;
                border-radius: var(--border-radius);
                box-shadow: 0 6px 28px rgba(50,60,100,0.14);
                display: flex;
                flex-direction: column;
                gap: 18px;
                border: 1.6px solid #eef0fa;
                position: relative;
            }
            input[type="text"] {
                padding: 12px 13px;
                border-radius: 7px;
                border: 1.3px solid var(--input-border);
                font-size: 1.15rem;
                transition: border-color 0.2s, box-shadow 0.2s;
                background: #f8fafc;
                color: #232345;
                box-shadow: 0 1px 3px #e6e8fa17;
            }
            input[type="text"]:focus {
                border-color: var(--input-focus);
                outline: none;
                box-shadow: 0 0 0 2px #2563eb29;
                background: #fff;
            }
            input[type="submit"] {
                background: var(--submit-bg);
                color: #fff;
                border: none;
                border-radius: 6px;
                padding: 12px 0;
                font-size: 1.12rem;
                cursor: pointer;
                font-weight: 600;
                letter-spacing: .025em;
                transition: background 0.19s, transform 0.08s;
                box-shadow: 0 1px 4px #2563eb22;
            }
            input[type="submit"]:hover, input[type="submit"]:focus {
                background: var(--submit-hover);
                transform: scale(1.025);
            }
            hr {
                border: none;
                border-top: 1.5px solid #e4e2ee;
                margin: 10px 0 0 0;
            }
            h3 {
                text-align: center;
                color: var(--primary);
                margin-top: 20px;
                letter-spacing: 0.02em;
                font-size: 1.27rem;
                background: #f5f7fe;
                padding: 10px 0 7px 0;
                border-radius: 8px;
                margin-bottom: 12px;
                box-shadow: 0 0.5px 0 #dedcf7;
                width: 390px;
                margin-left: auto;
                margin-right: auto;
            }
            p {
                text-align: center;
                color: #bd1740;
                font-style: italic;
                margin-top: 18px;
                background: #fff7fa;
                padding: 10px 0;
                border-radius: 7px;
                width: 390px;
                margin-left: auto;
                margin-right: auto;
                font-size: 1.09rem;
            }
            .results-list {
                background: #fafeff;
                margin: 30px auto 10px auto;
                padding: 15px 20px 10px 20px;
                width: 390px;
                border-radius: 9px;
                box-shadow: 0 1.5px 7px #e3edfa54;
                font-size: 1.085rem;
                color: #273444;
            }
            .results-list .result-item {
                padding: 6px 0 4px 0;
                border-bottom: 1px solid #ebe8fa;
                display: flex;
                gap: 16px;
                align-items: center;
            }
            .results-list .result-item:last-child {
                border-bottom: none;
            }
            .results-list .country-code {
                font-weight: 600;
                background: #f1f5fb;
                color: #2563eb;
                padding: 2px 8px;
                margin-right: 6px;
                border-radius: 5px;
                font-size: .97em;
                letter-spacing: .02em;
                min-width: 50px;
                text-align: center;
                display: inline-block;
            }
            @media (max-width: 480px) {
                form, h3, p, .results-list {
                    width: 99vw !important;
                    min-width: 0;
                    max-width: 99vw;
                    padding-left: 8vw;
                    padding-right: 8vw;
                }
            }
        </style>
    </head>

    <body>
        <form action="" autocomplete="off">
            <input type="text" name="ciutat" placeholder="Cerca país per nom..." value="<?= isset($_GET['ciutat']) ? htmlspecialchars($_GET['ciutat']) : '' ?>">
            <hr>
            <input type="submit" value="Cercar">
        </form>

        <?php
        //connexió dins block try-catch:
        //  prova d'executar el contingut del try
        //  si falla executa el catch
        try {
            $hostname = "localhost";
            $dbname = "world";
            $username = "admin";
            $pw = "admin1234";
            $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", "$username", "$pw");
        } catch (PDOException $e) {
            // obtenim missatge d'error de l'excepció llançada
            echo "<p>Error connectant a la BD: " . $e->getMessage() . "</p>\n";
            exit;
        }

        try {
            //preparem i executem la consulta
            if (isset($_GET['ciutat']) && !empty($_GET['ciutat'])) {
                // Si se ha enviado el formulario con texto, buscar países que contengan ese texto
                $query = $pdo->prepare("SELECT name, code FROM country WHERE name LIKE :busqueda ORDER BY name");
                $query->bindValue(':busqueda', '%' . $_GET['ciutat'] . '%');
                $query->execute();
                echo "<h3>Resultats per: '" . htmlspecialchars($_GET['ciutat']) . "'</h3>";
            } else {
                // Si no se ha enviado nada, mostrar todos los países
                $query = $pdo->prepare("SELECT name, code FROM country ORDER BY name");
                $query->execute();
            }
        } catch (PDOException $e) {
            // alternativa: obtenim missatge d'error de $query
            $err = $query->errorInfo();
            if ($err[0] != '00000') {
                echo "<p>Error accedint a dades: " . htmlspecialchars($err[2]) . "</p>";
                exit;
            }
        }

        //anem agafant les fileres una a una i mostrem de forma estilitzada
        $row = $query->fetch();
        if ($row) {
            echo '<div class="results-list">';
            do {
                echo '<div class="result-item"><span class="country-code">'.htmlspecialchars($row['code']).'</span> <span class="country-name">'.htmlspecialchars($row['name']).'</span></div>';
                $row = $query->fetch();
            } while ($row);
            echo '</div>';
        } else {
            if (isset($_GET['ciutat']) && !empty($_GET['ciutat'])) {
                echo "<p>No s'han trobat resultats per: '" . htmlspecialchars($_GET['ciutat']) . "'</p>";
            }
        }

        //versió alternativa amb foreach
        /*foreach ($query as $row) {
        echo $row['i']." - " . $row['a']. "<br/>";
    }*/

        //eliminem els objectes per alliberar memòria 
        unset($pdo);
        unset($query);
        ?>

    </body>

</html>