/**
 * Склонение слов в зависимости от числительного
 * Example: wordCase(count, ['комментарий', 'комментария', 'комментариев']);
 * @param num - число
 * @param words - массив с вариантами слова
 * @return {*}
 */
export const wordCase = function (num, words) {
  const cases = [2, 0, 1, 1, 1, 2]
  return words[
    num % 100 > 4 && num % 100 < 20 ? 2 : cases[num % 10 < 5 ? num % 10 : 5]
  ]
}
