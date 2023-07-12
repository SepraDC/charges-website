import { Charge } from "~/@type/Charge";

export interface User {
  userIdentifier: string;
  charges?: Charge[];
  roles: string[];
}
