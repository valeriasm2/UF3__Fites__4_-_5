<!DOCTYPE html>
<html lang="ca">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Exer5-2</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                max-width: 1200px;
                margin: 0 auto;
                padding: 20px;
            }
            .search-form {
                margin-bottom: 30px;
                padding: 20px;
                background-color: #f5f5f5;
                border-radius: 5px;
            }
            .search-form input[type="text"] {
                width: 300px;
                padding: 8px;
                font-size: 16px;
                border: 1px solid #ccc;
                border-radius: 4px;
            }
            .search-form input[type="submit"] {
                padding: 8px 20px;
                background-color: #007bff;
                color: white;
                border: none;
                border-radius: 4px;
                cursor: pointer;
            }
            .search-form input[type="submit"]:hover {
                background-color: #0056b3;
            }
            .results-table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }
            .results-table th, .results-table td {
                border: 1px solid #ddd;
                padding: 12px;
                text-align: left;
            }
            .results-table th {
                background-color: #f8f9fa;
                font-weight: bold;
            }
            .results-table tr:nth-child(even) {
                background-color: #f8f9fa;
            }
            .results-table tr:hover {
                background-color: #e9ecef;
            }
            .official-yes {
                color: #28a745;
                font-weight: bold;
            }
            .official-no {
                color: #dc3545;
            }
            .no-results {
                text-align: center;
                padding: 40px;
                color: #666;
                font-style: italic;
            }
            .search-info {
                margin-bottom: 20px;
                padding: 10px;
                background-color: #e7f3ff;
                border-left: 4px solid #007bff;
            }
        </style>
    </head>

    <body>
        <h1>Filtrar Llengües per País</h1>
        
        <div class="search-form">
            <form action="" method="GET" autocomplete="off">
                <label for="pais">Nom del país:</label><br>
                <input type="text" id="pais" name="pais" 
                       placeholder="Introdueix el nom del país (coincidència parcial)..." 
                       value="<?= isset($_GET['pais']) ? htmlspecialchars($_GET['pais']) : '' ?>">
                <input type="submit" value="Cercar">
            </form>
        </div>

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
            if (isset($_GET['pais']) && !empty(trim($_GET['pais']))) {
                // Si se ha enviado el formulario con texto, buscar lenguajes de países que contengan ese texto
                $query = $pdo->prepare("
                    SELECT c.Name as pais_nom, cl.Language as llengua, 
                           cl.IsOfficial as oficial, cl.Percentage as percentatge
                    FROM country c
                    INNER JOIN countrylanguage cl ON c.Code = cl.CountryCode
                    WHERE c.Name LIKE :busqueda
                    ORDER BY c.Name, cl.Percentage DESC
                ");
                $query->bindValue(':busqueda', '%' . trim($_GET['pais']) . '%');
                $query->execute();
                
                echo "<div class='search-info'>";
                echo "<h3>Resultats per: '" . htmlspecialchars(trim($_GET['pais'])) . "'</h3>";
                echo "</div>";
            } else {
                // Si no se ha enviado nada, mostrar todos los lenguajes de todos los países
                $query = $pdo->prepare("
                    SELECT c.Name as pais_nom, cl.Language as llengua, 
                           cl.IsOfficial as oficial, cl.Percentage as percentatge
                    FROM country c
                    INNER JOIN countrylanguage cl ON c.Code = cl.CountryCode
                    ORDER BY c.Name, cl.Percentage DESC
                ");
                $query->execute();
            }
        } catch (PDOException $e) {
            $err = $query->errorInfo();
            if ($err[0] != '00000') {
                echo "<p>Error accedint a dades: " . htmlspecialchars($err[2]) . "</p>";
                exit;
            }
        }

        // Mostrar resultados en tabla
        $row = $query->fetch();
        if ($row) {
            echo '<table class="results-table">';
            echo '<thead>';
            echo '<tr>';
            echo '<th>Nom del país</th>';
            echo '<th>Llengua</th>';
            echo '<th>Oficial</th>';
            echo '<th>Percentatge</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            
            do {
                $oficial_class = ($row['oficial'] == 'T') ? 'official-yes' : 'official-no';
                $oficial_text = ($row['oficial'] == 'T') ? 'Sí' : 'No';
                
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['pais_nom']) . '</td>';
                echo '<td>' . htmlspecialchars($row['llengua']) . '</td>';
                echo '<td class="' . $oficial_class . '">' . $oficial_text . '</td>';
                echo '<td>' . htmlspecialchars($row['percentatge']) . '%</td>';
                echo '</tr>';
                
                $row = $query->fetch();
            } while ($row);
            
            echo '</tbody>';
            echo '</table>';
        } else {
            if (isset($_GET['pais']) && !empty(trim($_GET['pais']))) {
                echo "<div class='no-results'>";
                echo "<p>No s'han trobat resultats per al país: '" . htmlspecialchars(trim($_GET['pais'])) . "'</p>";
                echo "</div>";
            } else {
                echo "<div class='no-results'>";
                echo "<p>No hi ha dades disponibles.</p>";
                echo "</div>";
            }
        }

        //eliminem els objectes per alliberar memòria 
        unset($pdo);
        unset($query);
        ?>

    </body>

</html>