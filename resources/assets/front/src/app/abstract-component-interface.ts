
export interface AbstractComponentInterface {
    editavel: boolean;

    readonly LOADING:number;
    readonly COMPLETE:number;
    readonly ERROR:number ;
    status: number;

    aviso:string;
    erroAviso:boolean;


    erroHttp (response):string;

}