function getInput() {
  const name = document.getElementById("name").value.toUpperCase();
  const year = document.getElementById("year").value.toUpperCase();
  const course = document.getElementById("course").value.toUpperCase();
  const key = parseInt(document.getElementById("key").value);

  const error = document.getElementById("error");
  error.textContent = "";

  if (!name || !year || !course || !key) {
    error.textContent = "Fill all fields!";
    return null;
  }

  if (key < 1 || key > 25) {
    error.textContent = "Key must be 1–25";
    return null;
  }

  return {
    text: `${name} | ${year} | ${course}`,
    key: key
  };
}

function caesar(text, key, mode) {
  let result = "";

  for (let char of text) {
    if (char >= 'A' && char <= 'Z') {
      let code = char.charCodeAt(0) - 65;

      code = mode === "encrypt"
        ? (code + key) % 26
        : (code - key + 26) % 26;

      result += String.fromCharCode(code + 65);
    } else {
      result += char;
    }
  }

  return result;
}

function encrypt() {
  const input = getInput();
  if (!input) return;

  document.getElementById("plaintext").textContent = input.text;
  document.getElementById("result").textContent =
    caesar(input.text, input.key, "encrypt");
}

function decrypt() {
  const input = getInput();
  if (!input) return;

  document.getElementById("plaintext").textContent = input.text;
  document.getElementById("result").textContent =
    caesar(input.text, input.key, "decrypt");
}

function undoAll() {
  document.getElementById("name").value = "";
  document.getElementById("year").value = "";
  document.getElementById("course").value = "";
  document.getElementById("key").value = "";

  document.getElementById("plaintext").textContent = "Waiting for input...";
  document.getElementById("result").textContent = "Result will appear here";
  document.getElementById("error").textContent = "";
}