<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="img/vitalis-logo.png" type="image/x-icon">
    <link rel="stylesheet" href="css/aula.css">
    <title>Vitalis - Aulas</title>
</head>
    <header>
        <nav>
            <ul>
                <li><a id="inicio" href="index.php">Início</a></li>
                <li><a href="aluno.php">Página do aluno</a></li>
                <li><a href="instrutor.php">Página do instrutor </a></li>
                <li><a href="aula.php">Aulas</a></li>
            </ul>
        </nav>        
    </header>
    <main>
        <?php
        include 'conexao.php';

        $sql_mostrar = "SELECT aula.aula_cod, aluno.aluno_nome AS aluno_nome, aula.aula_tipo, aula.aula_data, instrutor.instrutor_nome AS instrutor_nome
                FROM aula
                JOIN aluno ON aula.fk_aluno_cod = aluno.aluno_cod
                JOIN instrutor ON aula.fk_instrutor_cod = instrutor.instrutor_cod ORDER BY aluno_nome";
        $mostrar = $conexao->query($sql_mostrar);
        ?>
        <div class='exibir'>
            <?php
            while($row = $mostrar->fetch_assoc()){
                echo "
                <form action='' method='POST' class='info-aula'>
                    <input type='hidden' name='action' value='edit'>
                    <input type='hidden' name='id_aula' value='".$row['aula_cod']."'>

                    <div class='cont-atual'>
                        <p>Aluno: ".$row['aluno_nome']."</p>
                        <p>Aula: ".$row['aula_tipo']."</p>
                        <p>Data: ".$row['aula_data']."</p>
                        <p>Instrutor: ".$row['instrutor_nome']."</p>
                    </div>

                    <div class='edicao'>
                        <input id='n-nome' name='novo_aluno' type='text' value='".$row['aluno_nome']."' required>
                        <input id='n-tipo' name='novo_tipo' type='text' value='".$row['aula_tipo']."' required>
                        <input id='n-data' name='nova_data' type='date' value='".$row['aula_data']."' required>
                        <input id='n-inst' name='novo_inst' type='text' value='".$row['instrutor_nome']."' required>
                    </div>

                    <div class='bot-acoes'>
                        <button id='salv' type='submit'>Salvar</button>
                        <button id='canc' type='button'>Cancelar</button>
                    </div>
                    <div class='bot-acoes'>
                        <button id='edi' type='button'>Editar</button>
                        <button id='exc' type='submit' name='action' value='delete'>Excluir</button>
                    </div>
                </form>";
            }

            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                if (isset($_POST['action'])) {
                    if ($_POST['action'] === 'delete' && isset($_POST['id_aula'])) {
                        $id = $_POST['id_aula'];
                        $query = "DELETE FROM aula WHERE aula_cod = ?"; 
                        $stmt = $conexao->prepare($query);
                        $stmt->bind_param("i", $id);
                        
                        if ($stmt->execute()) {
                            echo "<script>location.href = 'aula.php';</script>";
                        } else {
                            echo "Erro ao excluir linha: ".$conexao->error;
                        }
                        
                        $stmt->close();
                    }
                        
                    if ($_POST['action'] === 'edit' && isset($_POST['id_aula'])) {
                        $id = $_POST['id_aula'];
                        $novo_aluno = $_POST['novo_aluno'];
                        $novo_tipo = $_POST['novo_tipo'];
                        $nova_data = $_POST['nova_data'];
                        $novo_inst = $_POST['novo_inst'];
                                
                        $query1 = "UPDATE aluno 
                                   JOIN aula ON aluno.aluno_cod = aula.fk_aluno_cod 
                                    SET aluno.aluno_nome = ? 
                                    WHERE aula.aula_cod = ?";
                        $stmt1 = $conexao->prepare($query1);
                        $stmt1->bind_param("si", $novo_aluno, $id);
                        $stmt1->execute();
                        $stmt1->close();
                                
                        $query2 = "UPDATE instrutor 
                                    JOIN aula ON instrutor.instrutor_cod = aula.fk_instrutor_cod 
                                    SET instrutor.instrutor_nome = ? 
                                    WHERE aula.aula_cod = ?";
                        $stmt2 = $conexao->prepare($query2);
                        $stmt2->bind_param("si", $novo_inst, $id);
                        $stmt2->execute();
                        $stmt2->close();
                                
                        $query3 = "UPDATE aula SET aula_tipo = ?, aula_data = ? WHERE aula_cod = ?";
                        $stmt3 = $conexao->prepare($query3);
                        $stmt3->bind_param("ssi", $novo_tipo, $nova_data, $id);
                                
                        if ($stmt3->execute()) {
                            header("Location:aula.php");
                        } else {
                            echo "Erro ao editar linha: " . $conexao->error;
                        }
                                
                        $stmt3->close();
                    }                                                            
                }
            }
            ?>
        
        <script>
            let butEdit = document.querySelectorAll('#edi')
            let edicao = document.querySelectorAll('.edicao')
            let conteudo = document.querySelectorAll('.cont-atual')
            let butExc = document.querySelectorAll('#exc')
            let butCanc = document.querySelectorAll('#canc')
            let butSalv = document.querySelectorAll('#salv')

            butCanc.forEach((botao, index) => {
                botao.addEventListener('click', () => {
                    edicao[index].classList.toggle('esconder');
                    conteudo[index].classList.toggle('mostrar');
                    butEdit[index].classList.toggle('mostrar');
                    butExc[index].classList.toggle('mostrar');
                    butSalv[index].classList.toggle('esconder');
                    butCanc[index].classList.toggle('esconder');
                })
            })

            butEdit.forEach((botao, index) => {
                botao.addEventListener('click', () => {
                    edicao[index].classList.toggle('mostrar');
                    conteudo[index].classList.toggle('esconder');
                    butEdit[index].classList.toggle('esconder');
                    butExc[index].classList.toggle('esconder');
                    butSalv[index].classList.toggle('mostrar');
                    butCanc[index].classList.toggle('mostrar');
                });
            });
        </script>
        </div>
    </main>
</body>
</html>