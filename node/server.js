import { WebSocketServer } from 'ws';
import express from 'express';
import cors from "cors";

// Inicializa o servidor WebSocket
const wss = new WebSocketServer({ port: 8080 });
const app = express();
app.use(express.json(), cors({origin: '*'}));
//app.use(cors({origin: '*'}));
const port = 3000;

// Armazena clientes ativos por ID
const computers = new Map();

wss.on('connection', (ws) => {
  console.log('Cliente conectado.');

  // Aguarda o cliente enviar seu ID
  ws.on('message', (message) => {
    const data = JSON.parse(message);

    // Registro inicial do cliente
    if (data.type === 'register') {
      const computerKey = data.computerKey;
      computers.set(computerKey, ws);
      console.log(`Cliente registrado: ID ${computerKey}`);
      ws.send(JSON.stringify({ type: 'registered', message: `Cliente ID ${computerKey} registrado com sucesso.` }));
    }
  });

  // Remove cliente desconectado
  ws.on('close', () => {
    for (const [computerKey, computerWs] of computers.entries()) {
      if (computerWs === ws) {
        computers.delete(computerKey);
        console.log(`Computador ID ${computerKey} desconectado.`);
        break;
      }
    }
  });
});

// Enviar comando para cliente específico
function sendPrintCommand(computerKey, fileUrl) {
  try {
    const computer = computers.get(computerKey);
    if (computer) {
      computer.send(JSON.stringify({ type: 'print', fileUrl }));
      console.log(`Comando de impressão enviado para o computador KEY: ${computerKey}`);
      return true;
    }  
  } catch (error) {
    console.error(`computador KEY: ${computerKey} - ${error.message}.`);
    return false;
  }
}

console.log('Servidor WebSocket rodando na porta 8080.');

app.post('/imprimir', async (req,res) => {
    //const fileUrl = 'https://www.ootech.com.br/fracionar-imprimir-material?id=6&dt_venc=06/10/2024';
    //sendPrintCommand('12345', fileUrl);
    let obj = {};
    const computer = req.body.computador;
    const etiqueta = req.body.etiqueta;
    
    if (computer=='' && etiqueta=='') {
      obj = {'error':true, 'type':'danger', 'msg':'Computador ou impressora não informado!'};
    } else if (!computers.get(computer)) {
      obj = {'error':true, 'type':'danger', 'msg':'Impressora não conectado!'};
    } else {
      const fg = sendPrintCommand(computer, etiqueta);
      if (fg) {
        obj = {'success':true, 'type':'success', 'msg':'Impressão enviada com sucesso!'};
      } else {
        obj = {'error':true, 'type':'danger', 'msg':'Erro inesperado, entre em contato com o administrador do sistema!'};
      }
    }
    
    res.setHeader('Content-Type', 'application/json');
    res.send(JSON.stringify(obj)).status(200);
});

app.listen(port, ()=> {
    console.log('Servidor http rodando na porta '+port+'!');
});



