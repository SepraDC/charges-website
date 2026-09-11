export const LOGIN_PATH = "/login";

/**
 * Only allow same-origin absolute paths as a post-login destination.
 * Rejects protocol-relative ("//evil.com", "/\evil.com") and absolute URLs to
 * prevent an open redirect through the `redirect` query param. Non-string
 * input is tolerated: callers pass raw query values and DOM events.
 */
export const sanitizeRedirect = (value: unknown): string => {
	const path = Array.isArray(value) ? value[0] : value;

	if (typeof path !== "string" || path.length === 0) return "/";
	if (!path.startsWith("/")) return "/";
	if (path.startsWith("//") || path.startsWith("/\\")) return "/";
	if (path.startsWith(LOGIN_PATH)) return "/";

	return path;
};

/** Login URL carrying the page the user was denied, so we can come back to it. */
export const loginUrlFor = (fullPath?: unknown): string => {
	const target = sanitizeRedirect(fullPath);

	return target === "/"
		? LOGIN_PATH
		: `${LOGIN_PATH}?redirect=${encodeURIComponent(target)}`;
};
