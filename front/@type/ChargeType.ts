import { Charge } from "./Charge";

export interface ChargeType {
  "@id": string;
  name: string;
  charges?: Charge[];
}
