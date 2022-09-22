export class Buscador {

    id: number;
    chave:string;
    entrada:string;

	constructor(id:(number|any), chave?:string, entrada?:string) {
        if (typeof id == "number") {
            this.id = id;
            this.chave = chave;
            this.entrada = entrada;
        }
        else {
            this.id = id.id;
            this.chave = id.chave;
            this.entrada = id.entrada;
        }
    }
    
    clone () {
        return new Buscador (this.id, this.chave, this.entrada)
    }
	
}