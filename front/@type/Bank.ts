import { Charge } from "../@type/Charge";

export interface Bank {
  "@id": string;
  id: number;
  name: string;
  abbreviation: string;
  image?: string;
  charges?: Charge[];
}
