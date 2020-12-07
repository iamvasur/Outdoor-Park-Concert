console.log("INIT APPLICATION")

function initSeats(){
    fetch('./backend/server.php', {
        headers:
    })
    .then(resp=>resp.json())
    .then(function(response){
        console.log(response)
    })
}
initSeats();
