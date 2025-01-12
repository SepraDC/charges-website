import type { Charge } from "@type/Charge";

export interface ChargeType {
	"@id": string;
	name: string;
	charges?: Charge[];
}
