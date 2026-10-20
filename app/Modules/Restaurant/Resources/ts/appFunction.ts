export function escapexHTML(value: string): string{
    return value.replace(/[&<>"']/g, (char) => {
        const entities: Record<string, string> = {
            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': "&quot;",
            "'": "&#39;"
        };
        return entities[char];
    });
}

export function escapeCreateDOMHtml(value: string): string {
    const element = document.createElement('div');
    element.textContent = value;
    return element.innerHTML;
}

export function setElementText(id: string, value: string): void{
    const element = document.getElementById(id);
    if(element)element.textContent = value;
}