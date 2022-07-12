<?php

require __DIR__.'/includes/app.php';

use \App\Http\Router;

//INICIA O ROUTER
$obRouter = new Router(URL);

//INCLUI AS ROTAS DO ADMINISTRADOR PAINEL
include __DIR__.'/routes/admin.php';

//INCLUI AS ROTAS DE PÁGINAS
include __DIR__.'/routes/client.php';

//IMPRIME O RESPONSE DA ROTA
$obRouter->run()->sendResponse();