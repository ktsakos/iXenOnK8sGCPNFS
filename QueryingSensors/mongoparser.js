var http = require('http');
var mqg = require('mongo-query-generator');

http.createServer(function (req, res) {

      if (req.method == 'POST') {
        var body = '';
        var syntax_error=false;
        var query="";
        req.on('data', function (data) {
            body += data;
            console.log( body);
            try {
                  query = mqg(body);
            }
            catch(error) {
                  syntax_error=true;
           }
           if(syntax_error){
             res.writeHead(200, {'Content-Type': 'application/json'});
             res.end("Syntax error");
             console.log("Wrong!!");
           }else{
             res.writeHead(200, {'Content-Type': 'application/json'});
             res.end(JSON.stringify(query, null , 2));
             console.log(JSON.stringify(query, null , 2));
             console.log("********");
             console.log(query);
           }

        });

    }



}).listen(1024);
