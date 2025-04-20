<?php

/**
 * AgendaController
 * 
 * Controlador para la gestión de agenda de eventos
 * Permite visualizar, filtrar y organizar eventos
 */
class AgendaController
{
    private $agendaModel;
    private $empresaModel;
    private $artistaModel;
    private $clienteModel;
    private $empresa_id; // Añadida como propiedad de la clase

    /**
     * Constructor
     * Inicializa los modelos necesarios y verifica autenticación
     */
    public function __construct()
    {
        // Verificar autenticación del usuario
        if (!isUserLoggedIn()) {
            $_SESSION['error_login'] = "Acceso denegado. Se requiere iniciar sesión.";
            header("Location:" . base_url . "user/login");
            exit();
        }

        // Cargar los modelos necesarios
        $this->agendaModel = new Agenda();
        $this->empresaModel = class_exists('Empresa') ? new Empresa() : null;
        $this->artistaModel = null; // Esto es por mientras se crea el modelo Artista
        $this->clienteModel = null; // Esto es por mientras se crea el modelo Cliente

        // Si no hay empresa asignada al usuario, redirigir
        $usuario = $_SESSION['user'];
        $db = Database::connect();
        $query = "SELECT id FROM empresas WHERE usuario_id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $usuario->id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            $_SESSION['error_message'] = "No tienes una empresa asignada";
            header("Location:" . base_url . "user/logout");
            exit();
        } else {
            // Guardar el ID de la empresa en una propiedad para usarla en todas las acciones
            $empresa = $result->fetch_object();
            $this->empresa_id = $empresa->id;
        }
        $stmt->close();
    }

    /**
     * Acción por defecto del controlador
     * Muestra la lista de eventos
     */
    public function index()
    {
        // Configurar el título de la página
        $pageTitle = "Agenda de Eventos";

        // Obtener parámetros de filtro (si existen)
       // $estado = isset($_GET['estado']) ? $_GET['estado'] : null;
      //  $fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : null;
     //   $fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : null;

        // Obtener todos los eventos de la empresa actual
     //   $eventos = $this->agendaModel->getAllEventos($this->empresa_id, $estado, $fecha_inicio, $fecha_fin);

        // Obtener contadores por estado para mostrar estadísticas
      //  $contadores = $this->agendaModel->countEventosPorEstado($this->empresa_id);

        // Incluir la vista
        require_once 'views/user/agenda/index.php';
    }

    /**
     * Ver detalles de un evento específico
     * @param int $id ID del evento
     */
    public function ver($id)
    {
        // Configurar el título de la página
        $pageTitle = "Detalles del Evento";

        // Obtener el evento
        $evento = $this->agendaModel->getEventoById($id, $this->empresa_id);

        // Verificar que el evento existe y pertenece a la empresa del usuario
        if (!$evento) {
            $_SESSION['error_message'] = "Evento no encontrado o no tienes permiso para verlo";
            header("Location:" . base_url . "agenda/index");
            exit();
        }

        // Incluir la vista
        require_once 'views/agenda/ver.php';
    }

    /**
     * Muestra la agenda en formato calendario
     */
    public function calendario()
    {
        // Configurar el título de la página
        $pageTitle = "Calendario de Eventos";

        // Por defecto, mostrar el mes actual
        $mes_actual = date('m');
        $anio_actual = date('Y');

        // Si se proporcionan parámetros de mes y año, usarlos
        if (isset($_GET['mes']) && isset($_GET['anio'])) {
            $mes_actual = intval($_GET['mes']);
            $anio_actual = intval($_GET['anio']);

            // Validar mes y año
            if ($mes_actual < 1 || $mes_actual > 12) {
                $mes_actual = date('m');
            }
            if ($anio_actual < 2000 || $anio_actual > 2100) {
                $anio_actual = date('Y');
            }
        }

        // Calcular primer y último día del mes seleccionado
        $primer_dia = sprintf('%04d-%02d-01', $anio_actual, $mes_actual);
        $ultimo_dia = date('Y-m-t', strtotime($primer_dia));

        // Obtener eventos para el mes seleccionado
        $eventos = $this->agendaModel->getEventosCalendario($this->empresa_id, $primer_dia, $ultimo_dia);

        // Preparar datos para la vista del calendario
        $calendario = [];
        foreach ($eventos as $evento) {
            $fecha = $evento->fecha_evento;
            if (!isset($calendario[$fecha])) {
                $calendario[$fecha] = [];
            }
            $calendario[$fecha][] = $evento;
        }

        // Incluir la vista
        require_once 'views/agenda/calendario.php';
    }

    /**
     * Muestra la vista de próximos eventos
     */
    public function proximos()
    {
        // Configurar el título de la página
        $pageTitle = "Próximos Eventos";

        // Obtener los próximos eventos (por defecto 10)
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
        $eventos = $this->agendaModel->getProximosEventos($this->empresa_id, $limit);

        // Incluir la vista
        require_once 'views/agenda/proximos.php';
    }

    /**
     * Muestra eventos para una fecha específica
     * @param string $fecha Fecha en formato Y-m-d
     */
    public function fecha($fecha)
    {
        // Validar formato de fecha
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            $_SESSION['error_message'] = "Formato de fecha inválido";
            header("Location:" . base_url . "agenda/index");
            exit();
        }

        // Configurar el título de la página
        $pageTitle = "Eventos del " . date('d/m/Y', strtotime($fecha));

        // Obtener eventos para esa fecha
        $eventos = $this->agendaModel->getEventosCalendario($this->empresa_id, $fecha, $fecha);

        // Incluir la vista
        require_once 'views/agenda/fecha.php';
    }

    /**
     * Exporta eventos a CSV
     */
    public function exportar()
    {
        // Obtener parámetros de filtro (si existen)
        $estado = isset($_GET['estado']) ? $_GET['estado'] : null;
        $fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : null;
        $fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : null;

        // Obtener eventos con los filtros aplicados
        $eventos = $this->agendaModel->getAllEventos($this->empresa_id, $estado, $fecha_inicio, $fecha_fin);

        // Verificar que hay eventos para exportar
        if (empty($eventos)) {
            $_SESSION['error_message'] = "No hay eventos para exportar con los filtros seleccionados";
            header("Location:" . base_url . "agenda/index");
            exit();
        }

        // Preparar el archivo CSV
        $filename = 'eventos_' . date('Y-m-d') . '.csv';

        // Configurar encabezados para descarga
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // Crear el puntero de salida
        $output = fopen('php://output', 'w');

        // Añadir BOM para UTF-8
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Escribir encabezados de columnas
        fputcsv($output, [
            'ID',
            'Nombre',
            'Fecha',
            'Hora',
            'Cliente',
            'Artista',
            'Ciudad',
            'Lugar',
            'Valor',
            'Estado'
        ]);

        // Escribir datos
        foreach ($eventos as $evento) {
            $cliente = isset($evento->cliente_nombre) ? $evento->cliente_nombre . ' ' . $evento->cliente_apellido : 'N/A';
            $artista = isset($evento->artista_nombre) ? $evento->artista_nombre : 'N/A';

            fputcsv($output, [
                $evento->id,
                $evento->nombre_evento,
                date('d/m/Y', strtotime($evento->fecha_evento)),
                $evento->hora_evento ? date('H:i', strtotime($evento->hora_evento)) : 'N/A',
                $cliente,
                $artista,
                $evento->ciudad_evento,
                $evento->lugar_evento,
                $evento->valor_evento,
                $evento->estado_evento
            ]);
        }

        fclose($output);
        exit;
    }
}
