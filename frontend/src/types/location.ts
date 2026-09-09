export interface City {
    id: number,
    name: string
}

export interface District {
    id: number,
    name: string,
    postal_code: string,
}

export interface DistrictWithCity extends District {
    city: City
}