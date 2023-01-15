/* const btnCopy = document.querySelector('.btn-copy');
const txtCopy = document.querySelector('.box-txt');

btnCopy.addEventListener('click', () => {
    navigator.clipboard.writeText(txtCopy.innerText);
})
 */
function clickCopy(event) {
    navigator.clipboard.writeText(event.target.previousElementSibling.textContent);
}