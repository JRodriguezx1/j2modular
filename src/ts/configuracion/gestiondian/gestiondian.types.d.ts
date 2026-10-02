type companiesDian = {
    id:string,
    identification_number:string, 
    business_name:string, 
    idsoftware:string, 
    token:string
}

//Resoluciones que devuelve la DIan
interface TechnicalKeyObject {
    _attributes: {
    nil: string;
    };
}
interface NumberRangeItem {
    idcompany?: string,
    ResolutionNumber: string;
    ResolutionDate: string;
    Prefix: string;
    FromNumber: string;
    ToNumber: string;
    ValidDateFrom: string;
    ValidDateTo: string;
    TechnicalKey: string | TechnicalKeyObject;
}