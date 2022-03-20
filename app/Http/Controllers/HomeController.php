<?php

namespace App\Http\Controllers;

use App\Models\Process;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    public function index(){
        $processes = Process::all();
        return view("welcome",["processes"=>$processes]);
    }
    
    public function runCommand(Request $request){
        $validated = $request->validate([
            'command' => 'required',
        ]);
        $command = $validated["command"];
        $handle = popen("start /B ". $command, "r");
        $commandCheck = Process::where("command","$command")->count();
        if($commandCheck>0){
            return redirect()->route('Home')->with('status', 'Command Already Existed');
        }else{
            $cmd = 'wmic process call create "'.$command.'" | find "ProcessId"';
            // $cmd = $command;
            $handle = popen("start /B ". $cmd, "r");
            $read = fread($handle, 200); 
            $pid=substr($read,strpos($read,'=')+1);
            $pid=substr($pid,0,strpos($pid,';') );
            $process = Process::create([
                "command"=> $validated["command"],
                "pid"=>(int)$pid
            ]);
            return redirect()->route('Home');
        }
    }

    public function run(Request $request){

        $process=Process::findOrFail($request->id);
        $oldpid = $process->pid;
        
        $result = shell_exec('tasklist /FI "PID eq '.$oldpid.'"' );
        //Check The status Of The Process
        if(count(preg_split("/\n/", $result))==2){
        $command = $process->command;
        $cmd = 'wmic process call create "'.$command.'" | find "ProcessId"';
        $handle = popen("start /B ". $cmd, "r");
        $read = fread($handle, 200); 
        $pid=substr($read,strpos($read,'=')+1);
        $pid=substr($pid,0,strpos($pid,';') );
        $process->pid=$pid;
        $process->save();
        return redirect()->route('Home')->with('status', 'Command Already Existed in DataBase ');
        }else{
            return redirect()->route('Home');
        }
        
    }

    // To Check The status Of The Process
    public function CheckProc(Request $request){
        $result = shell_exec('tasklist /FI "PID eq '.$request->pid.'"' );
        if (count(preg_split("/\n/", $result))>2) {
            return redirect()->route('Home')->with('statusCheckGood', 'Command Already Executed !');
        }else{
            return redirect()->route('Home')->with('statusCheckNotGood', 'Command Already Stoped !');
        }
    }

    // To Kill The Proccess After Check her Status
    public function KillPross(Request $request){

        $pid=$request->pid;
        $result = shell_exec('tasklist /FI "PID eq '.$pid.'"' );
        if (count(preg_split("/\n/", $result))>2) {
            shell_exec('taskkill /PID '.$pid.' /F' );
            return redirect()->route('Home')->with('statusCheckNotGood', 'Command Stoped successfuly !');
        }else{
            return redirect()->route('Home');
        }
    }
}
