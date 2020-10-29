const checkPassport = function(passportSer, passportNum) {
    return new Promise((resolve, reject) => {
        
        let xhr = new XMLHttpRequest();
        xhr.onload = function() {
            resolve (xhr.status);
        };
        xhr.open('GET', '/passport?' + 'passportSeries=' + passportSer + '&passportNumber=' + passportNum);
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.send();
    });
}

const PassportMonitor = function() {
    return(
        <div id="PassportMonitor" className="Passport__Monitor"></div>
    );
};

const PassportForm = function() {

    const [passportSer, setPassportSer] = React.useState('');
    const [passportNum, setPassportNum] = React.useState('');

    const submitHandler = function(event) {
        event.preventDefault();
       
        checkPassport(passportSer, passportNum).
            then(response => {
                let tile = document.createElement('div');
                let div = document.createElement('div');
                let monitorNumber = document.createElement('div');
                let monitorText = document.createElement('div');
                if (204 === response) {
                    tile.className += 'Passport__Monitor__Tile__Red ';
                    monitorText.innerHTML = 'Паспорт недействителен';
                }
                else {
                    tile.className += 'Passport__Monitor__Tile__Green ';
                    monitorText.innerHTML = 'Паспорт не входит в списки недействительных';
                }
                tile.className += 'Passport__Monitor__Tile';
                monitorNumber.id = 'MonitorNumber';
                monitorNumber.className = 'Passport__Monitor__Number';
                monitorNumber.innerHTML = passportSer + ' ' + passportNum;
                monitorText.className = 'Passport__Monitor__Text';
                document.querySelector('#PassportMonitor').prepend(tile);  
                tile.append(div);
                div.append(monitorNumber);
                div.append(monitorText);
            }
        );
    };

    const seriesHandler = function(event) {
        setPassportSer(event.target.value);
    };

    const numberHandler = function(event) {
        setPassportNum(event.target.value);
    };

    return(
        <form className="Passport__Form">
            <input type="text" name="passport_series" value={passportSer} onChange={seriesHandler} 
                className="Passport__Input" placeholder="Серия паспорта" />
            <input type="text" name="passport_number" value={passportNum} onChange={numberHandler} 
                className="Passport__Input" placeholder="Номер паспорта" />
            <input type="submit" value="Проверить" onClick={submitHandler} className="Passport__Submit" />
        </form> 
    );
};

const PageContainer = function(props) {
    return(
        <div className="Page__Container">
            {props.children}
        </div>
    );
}

const StatusPage = function() {
    return(
        <PageContainer>
            <h2 className="Passport__Header">Проверить паспорт РФ на недействительность</h2>
            <PassportForm />
            <PassportMonitor />
        </PageContainer>
    );
};

const Application = function() {
    return(
        <StatusPage />
    );
};

const ApplicationBlock = document.querySelector('#application'); 
ReactDOM.render(<Application />, ApplicationBlock);