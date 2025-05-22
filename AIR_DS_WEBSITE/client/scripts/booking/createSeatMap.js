/**
 * This function creates and add the seat map to the DOM
 * The seat map is seperated into upper, lower rows (grouped together in a div) 
 * and a middle row (has its own div) containing the numbers of the seat.
 * 
 * At the first column is the correspondin letter of each row.
 * The letters are added. 
 * This means that the 1st row has letter F
 *                     2nd row has letter D
 *                     ...
 *                    last row has letter A 

 * 
 * @param {Array} takenSeats - contains the ids of the taken seats.
 * the ids have the format: <row letter>-<seat number>
 * ex A-10 -> is the 10th seat of the last row (row lettering is backwards)
 */
export function createSeatMap(takenSeats) {
    // TODO this will be function parameter, for now keep empty
    // contains the ids of the taken seats
    // const takenSeats = ['A-13'];    

    const planeBody = document.getElementById('plane-body');

    // number of rows including number row
    const rows = 7;

    const middleRow = 3;

    // normally 31, one extra for each row letter
    const columns = 32;

    // these have regular numbering (not from 0)
    const upFrontSeats = 10; //without 1
    const legRoomSeats = [1, 11, 12];

    // create divs for the sets of rows
    // the seperation to upper, middle and lower rows is only for styling purposes
    // rows could be added as is to the plane body without first putting them into another div
    const upperRowDiv = document.createElement('div');
    const middleRowDiv = document.createElement('div');
    const lowerRowDiv = document.createElement('div');
    
    upperRowDiv.id = "upper-row-container";
    middleRowDiv.id = "middle-row-container";
    lowerRowDiv.id = "lower-row-container";

    // add each row to the corresponing part of the plane
    for (let row = 0; row < rows; row++) {
        if (row < middleRow) 
            addRow(upperRowDiv, rows, row, middleRow, columns, takenSeats);
        else if (row === middleRow) 
            addRow(middleRowDiv, rows, row, middleRow, columns, takenSeats);
        else 
            addRow(lowerRowDiv, rows, row, middleRow, columns, takenSeats);
    }

    planeBody.appendChild(upperRowDiv);
    planeBody.appendChild(middleRowDiv);
    planeBody.appendChild(lowerRowDiv);
}


function addRow(parentDiv, maxRows, currentRow, middleRow, maxSeats, takenSeats) {
    // they are added from top to bottom
    const rowLetter = ['A', 'B', 'C', 'D', 'E', 'F'];

    const rowDiv = document.createElement('div');
    rowDiv.className = 'row';
    
    // add the row letter as the first element of the row
    addRowLetter(rowDiv, maxRows, currentRow, middleRow, rowLetter, maxRows);

    // add seats to the rowDiv 
    addSeats(rowDiv, maxSeats, currentRow, middleRow, rowLetter, maxRows, takenSeats);

    // add rowDiv to parentDiv
    parentDiv.appendChild(rowDiv);
}

function addSeats(rowDiv, maxSeats, currentRow, middleRow, rowLetter, maxRows, takenSeats) {
    const upFrontSeats = 10;            //num of upfront seats
    const legRoomSeats = [1, 11, 12];   // the extra leg room seats

    // is the current row the middle row?
    let isMiddleRow = false;
    if (currentRow === middleRow) isMiddleRow = true;

    // start from 1 because there is also the letter elements
    for (let seat = 1; seat < maxSeats; seat++) {
        let isTaken = false;
        let isFront = false;
        let isLegSeat = false;

        // is the seat at the front?
        if (seat <= upFrontSeats) isFront = true;
        // is it extra leg room seat?
        if (legRoomSeats.includes(seat)) isLegSeat = true;

        // get the id of the seat 
        const letter = getRowLetter(currentRow, middleRow, rowLetter, maxRows);
        const id = `${letter}-${seat}`;

        // maybe change order
        if (takenSeats.includes(id)) isTaken = true;

        const seatDiv = setSeat(seat, id, isMiddleRow, isLegSeat, isFront, isTaken);
        rowDiv.appendChild(seatDiv);
    }
}

function setSeat(currentSeat, id, isMiddleRow, isLegSeat, isFront, isTaken = false) {
    const seat = document.createElement('div');
    seat.id = id;
    seat.className = 'seat';

    // TODO change the classes of each seat for easier coloring
    if (isMiddleRow) {
        seat.innerText = `${currentSeat}`;
        seat.className = 'seat-number';
    }
    else if (isLegSeat) {
        seat.style.backgroundColor = "#caddeb";
    }
    else if (isFront) {
        seat.style.backgroundColor = "#4d608b";
    }
    else {
        seat.style.backgroundColor = "#f4f4f4";
    }

    if (isTaken) {
        seat.style.backgroundColor = "#d92725";
        seat.className = "taken-seat";
    }

    return seat;
}

function addRowLetter(rowDiv, maxRows, currentRow, middleRow, rowLetter) {
    const letter = getRowLetter(currentRow, middleRow, rowLetter, maxRows)

    const letterDiv = document.createElement('div');
    letterDiv.className = 'rowLetter';
    letterDiv.innerText = letter   

    rowDiv.appendChild(letterDiv);
}

function getRowLetter(currentRow, middleRow, rowLetter, maxRows) {
    let letter = '';
    // numbering starts from the top but letters have opposite order
    // maxRows is equal to the number of rows but indexes at rowLetter start from 0,
    // so subtract 1
    if (currentRow < middleRow) {
        // subtract 1 because of middle row and 1 because 0 is the start
        letter = rowLetter[(maxRows - 2 - currentRow) % 7];
    }
    else if (currentRow === middleRow) {
        letter = 'R';
    }
    else {
        letter = rowLetter[(maxRows - 1 - currentRow) % 7];
    }
    return letter;
}