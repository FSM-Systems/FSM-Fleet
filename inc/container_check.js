function returnval(thecharacter) {
	switch(thecharacter) {
		case "A":
			return 10;
			break	;
		case "B":
			return 12;
			break	;
		case "C":
			return 13;
			break	;
		case "D":
			return 14;
			break	;
		case "E":
			return 15;
			break	;
		case "F":
			return 16;
			break	;
		case "G":
			return 17;
			break	;
		case "H":
			return 18;
			break	;
		case "I":
			return 19;
			break	;
		case "J":
			return 20;
			break	;
		case "K":
			return 21;
			break	;
		case "L":
			return 23;
			break	;
		case "M":
			return 24;
			break	;
		case "N":
			return 25;
			break	;
		case "O":
			return 26;
			break	;
		case "P":
			return 27;
			break	;
		case "Q":
			return 28;
			break	;
		case "R":
			return 29;
			break	;
		case "S":
			return 30;
			break	;
		case "T":
			return 31;
			break	;
		case "U":
			return 32;
			break	;
		case "V":
			return 34;
			break	;
		case "W":
			return 35;
			break	;
		case "X":
			return 36;
			break	;
		case "Y":
			return 37;
			break	;
		case "Z":
			return 38;
			break	;
	}
}

function containercheckdigit(cont) {
	cont = cont.replace(" ","").replace("-","").toUpperCase();

	var chars = cont.split("");

	if (cont.length != 11) {
		return false;
	}

	// Loop Through chars and calculate
	var sum = 0;
	var ret = 0;
	var mult = 0;
	for (c = 0; c <= 9; c++) {
		switch(c) {
			case 0:
				sum += parseInt(returnval(chars[c])) * 1;
				break;
			case 1:
				sum += parseInt(returnval(chars[c])) * 2;
				break;
			case 2:
				sum += parseInt(returnval(chars[c])) * 4;
				break;
			case 3:
				sum += parseInt(returnval(chars[c])) * 8;
				break;
			case 4:
				sum += parseInt(chars[c]) * 16;
				break;
			case 5:
				sum += parseInt(chars[c]) * 32;
				break;
			case 6:
				sum += parseInt(chars[c]) * 64;
				break;
			case 7:
				sum += parseInt(chars[c]) * 128;
				break;
			case 8:
				sum += parseInt(chars[c]) * 256;
				break;
			case 9:
				sum += parseInt(chars[c]) * 512;
				break;
		}
	}

	var totals = sum /11;

	var rounded = Math.floor(totals);

	digit = rounded * 11;

	ret = digit - sum;

	ret = Math.abs(ret);

	// 10 has to be converted to 0
	if (ret == 10) {
		ret = 0;
	}

	if (chars[10] == ret) {
		return true;
	} else {
		return false;
	}
}