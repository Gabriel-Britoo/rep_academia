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

        $sql_mostrar = "SELECT aula.aula_cod, aluno.aluno_cod AS fk_aluno_cod, aluno.aluno_nome, aula.aula_tipo, aula.aula_data, instrutor.instrutor_cod AS fk_instrutor_cod, instrutor.instrutor_nome 
                FROM aula
                JOIN aluno ON aula.fk_aluno_cod = aluno.aluno_cod
                JOIN instrutor ON aula.fk_instrutor_cod = instrutor.instrutor_cod ORDER BY aula_data";
        $mostrar = $conexao->query($sql_mostrar);
        ?>
        <div class='exibir scroller'>
            <?php
            while($row = $mostrar->fetch_assoc()){
                $sql_alunos = "SELECT aluno_cod, aluno_nome FROM aluno";
                $alunos = $conexao->query($sql_alunos);

                $sql_instrutores = "SELECT instrutor_cod, instrutor_nome FROM instrutor";
                $instrutores = $conexao->query($sql_instrutores);
                
                echo "
                <form action='' method='POST' class='info-aula'>
                    <input type='hidden' name='action' value='edit'>
                    <input type='hidden' name='id_aula' value='".$row['aula_cod']."'>

                    <div class='cont-atual'>
                        <p>Aluno: " . $row['aluno_nome'] . "</p>
                        <p>Aula: " . $row['aula_tipo'] . "</p>
                        <p>Data: " . $row['aula_data'] . "</p>
                        <p>Instrutor: " . $row['instrutor_nome'] . "</p>
                    </div>

                    <div class='edicao'>
                        <select name='novo_aluno' required>
                            ";
                            while ($aluno = $alunos->fetch_assoc()) {
                                echo "<option value='" . $aluno['aluno_cod'] . "' " . ($aluno['aluno_cod'] == $row['fk_aluno_cod'] ? 'selected' : '') . ">" . $aluno['aluno_nome'] . "</option>";
                            }
                            echo "
                        </select>

                        <input id='n-tipo' name='novo_tipo' type='text' value='" . $row['aula_tipo'] . "' required>
                        <input id='n-data' name='nova_data' type='date' value='" . $row['aula_data'] . "' required>

                        <select name='novo_inst' required>
                            ";
                            while ($instrutor = $instrutores->fetch_assoc()) {
                                echo "<option value='" . $instrutor['instrutor_cod'] . "' " . ($instrutor['instrutor_cod'] == $row['fk_instrutor_cod'] ? 'selected' : '') . ">" . $instrutor['instrutor_nome'] . "</option>";
                            }
                            echo "
                        </select>
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
                            echo "Erro ao excluir linha: " . $conexao->error;
                        }
                        
                        $stmt->close();
                    }
                    
                    if ($_POST['action'] === 'edit' && isset($_POST['id_aula'])) {
                        $id = $_POST['id_aula'];
                        $novo_aluno = $_POST['novo_aluno'];
                        $novo_tipo = $_POST['novo_tipo'];
                        $nova_data = $_POST['nova_data'];
                        $novo_inst = $_POST['novo_inst'];
                        
                        $query = "UPDATE aula SET fk_aluno_cod = ?, aula_tipo = ?, aula_data = ?, fk_instrutor_cod = ? WHERE aula_cod = ?";
                        $stmt = $conexao->prepare($query);
                        $stmt->bind_param("issii", $novo_aluno, $novo_tipo, $nova_data, $novo_inst, $id);

                        if ($stmt->execute()) {
                            echo "<script>location.href = 'aula.php';</script>";
                        } else {
                            echo "Erro ao editar aula: " . $conexao->error;
                        }
                        
                        $stmt->close();
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