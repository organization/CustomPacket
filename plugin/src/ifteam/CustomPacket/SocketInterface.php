<?

namespace ifteam\CustomPacket;

use pocketmine\Server;

class SocketInterface{
    
    private $internalThreaded;
    private $externalThreaded;
    private $server;
    private $socket;
    
    public function __construct(Server $server){
        $this->internalThreaded = new \Threaded();
        $this->externalThreaded = new \Threaded();
        $this->server = $server;
        $this->socket = new CustomSocket($this->internalThreaded, $this->externalThreaded, $this->server->getLogger(), 19131, $this->server->getIp() === "" ? "0.0.0.0" : $this->server->getIp());
    }
    
    public function process(){
        $work = false;
        $this->pushInternalQueue([chr(Info::SIGNAL_TICK)]);
        if($this->handlePacket()){
            $work = true;
            while($this->handlePacket());
        }
        return $work; //For future use. Not now.
    }
    
    public function handlePacket(){
        if(($packet = $this->readMainQueue()) instanceof DataPacket){
            //TODO
            return true;
        }
        
        return false;
    }
    
    public function shutdown(){
        $this->pushInternalQueue([chr(Info::SIGNAL_SHUTDOWN)]);
    }
    
    public function pushMainQueue(DataPacket $packet){
        $this->exteranlThreaded[] = $packet;
    }
    
    public function readMainQueue(){
        return $this->externalThreaded->shift();
    }
    
    public function pushInternalQueue(array $buffer){
        $this->internalThreaded[] = $buffer;
    }
    
    public function readInternalQueue(){
        return $this->internalThreaded->shift();
    }
    
}