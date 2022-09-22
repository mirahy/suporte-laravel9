export class Redimensionavel {

        elemento:any;
        base:number;
        min:number;
        max:number;
        personalizado:any; // {funcao, parametros }
    
        constructor(elemento:any,base,min?,max?,personalizado?) {
            this.elemento = elemento;
            this.base = parseInt(base);
            this.min = min != null ? parseInt(min) : null;
            this.max = max != null ? parseInt(max) : null;
            this.personalizado = personalizado;
        }

        executarPersonalizado () {
            this.personalizado.funcao (this.personalizado.parametros);
        }
    
    }
    