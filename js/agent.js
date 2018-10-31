/// <reference path="../typings/jquery/jquery.d.ts" />

var mAgent = {};

mAgent.myAgent = $('#myAgent');
mAgent.myAgentInput = $('#myAgent .agentText');
mAgent.agentSubmit = $('#myAgent #agentSubmit');


mAgent.agentSubmit.on('click',function(){
    mAgent.userName = mAgent.myAgentInput[0].value +'  '+ mAgent.myAgentInput[2].value +'  '+ mAgent.myAgentInput[3].value;
    mAgent.userPhone =mAgent.myAgentInput[1].value;
    var agentCode =2;
    submitUserInfo(mAgent.userName,mAgent.userPhone ,32,agentCode);

});

