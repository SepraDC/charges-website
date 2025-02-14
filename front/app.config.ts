export default defineAppConfig({
	ui: {
		colors: {
			primary: "gold",
			secondary: "blue",
			neutral: "zinc",
		},
		button: {
			compoundVariants: [
				{
					color: "neutral",
					variant: "solid",
					class: "bg-neutral-400",
				},
			],
		},
	},
});
