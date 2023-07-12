import { Bank } from "~/@type/Bank";
import { ChargeType } from "~/@type/ChargeType";
import { User } from "~/@type/User";

export interface Charge {
  "@id": string;
  id: number;
  name: string;
  amount: number;
  state: boolean;
  bank: Bank;
  chargeType?: ChargeType;
  user?: User;
  date?: string;
}

export type ChargeResponse = Charge;
