<? 
    class Dashboard{
        public $data_inicio;
        public $data_fim;
        public $numeroVendas;
        public $totalVendas;

        public function __get($atributo){
            return $this->$atributo;
        }

        public function __set($atributo, $valor){
            $this->$atributo = $valor;
            return $this;
        }
    }

    class Conexao{
        private $host = 'localhost';
        private $dbname = 'dashboard';
        private $user = 'root';
        private $pass = '';

        public function conectar(){
            try{
                $conexao = new PDO(
                    "mysql:host=$this->host;
                     dbname=$this->dbname", "$this->user", "$this->pass");
                $conexao->exec('set charset utf8');

                return $conexao;

            } catch(PDOException $e){
                echo "<p>". $e->getMessege()."</p>";
            }
        }

    }
    class Bd {
        private $conexao;
        private $dashboard;
        public function __construct($conexao, $dashboard)
        {
            $this->conexao = $conexao->conectar();
            $this->dashboard = $dashboard;
        }

        public function getNumeroVendas(){
            $sql = "SELECT COUNT(*) as numero_vendas FROM `tb_vendas` WHERE data_venda BETWEEN :data_inicio and :data_fim ";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
            $stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->numero_vendas;
        }
        public function getTotalVendas(){
            $sql = "SELECT
                        SUM(total) as total_vendas 
                    FROM `tb_vendas` 
                    WHERE 
                        data_venda 
                    BETWEEN :data_inicio and :data_fim ";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
            $stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->total_vendas;
        }
    }

    // Instanciando objetos

    $dashboard = new Dashboard();

    $dashboard->__set('data_inicio', '2018-08-01');
    $dashboard->__set('data_fim', '2018-08-31');

    $conexao = new Conexao();
    $bd = new Bd($conexao, $dashboard);
    $dashboard->__set('numeroVendas', $bd->getNumeroVendas());
    $dashboard->__set('totalVendas', $bd->getTotalVendas());
    print_r($dashboard);
?>