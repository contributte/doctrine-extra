<?php declare(strict_types = 1);

namespace Nettrine\Extra\Data;

final class QueryUtils
{

	public const KEYWORDS1 = 'SELECT|(?:ON\s+DUPLICATE\s+KEY)?UPDATE|INSERT(?:\s+INTO)?|REPLACE(?:\s+INTO)?|SHOW|DELETE|CALL|UNION|FROM|WHERE|HAVING|GROUP\s+BY|ORDER\s+BY|LIMIT|OFFSET|SET|VALUES|LEFT\s+JOIN|INNER\s+JOIN|TRUNCATE|START\s+TRANSACTION|COMMIT|ROLLBACK|(?:RELEASE\s+|ROLLBACK\s+TO\s+)?SAVEPOINT';
	public const KEYWORDS2 = 'ALL|DISTINCT|DISTINCTROW|IGNORE|AS|USING|ON|AND|OR|IN|IS|NOT|NULL|[RI]?LIKE|REGEXP|TRUE|FALSE';

	/**
	 * Highlight given SQL parts
	 */
	public static function highlight(string $sql): string
	{
		$sql = ' ' . $sql . ' ';
		$sql = htmlspecialchars($sql, ENT_IGNORE, 'UTF-8');
		$sql = preg_replace_callback(
			sprintf('#(/\\*.+?\\*/)|(?<=[\\s,(])(%s)(?=[\\s,)])|(?<=[\\s,(=])(%s)(?=[\\s,)=])#is', self::KEYWORDS1, self::KEYWORDS2),
			static function ($matches) {
				// @phpstan-ignore-next-line
				if ($matches[1] !== null) { // comment
					return '<em style="color:gray">' . $matches[1] . '</em>';
				}

				// @phpstan-ignore-next-line
				if ($matches[2] !== null) { // most important keywords
					return '<strong style="color:#2D44AD">' . $matches[2] . '</strong>';
				}

				if ($matches[3] !== null) { // other keywords
					return '<strong>' . $matches[3] . '</strong>';
				}

				return $matches;
			},
			$sql
		);

		return trim((string) $sql);
	}

}
