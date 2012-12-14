<?php
/**
 * Helper class for Repositories
 * 
 * @author Martin Kubíček <martin@wedesign.cz>
 */

class RepositoryUtils {
	/**
	 * Creates an unique slug from given string
	 * 
	 * @param BaseRepository $repository Repository
	 * @param string $string String to make slug from
	 * @param int $primaryKey Row primary key value, only needed when updating a record
	 * @param string $slugField Column which represents the slug value
	 *
	 * @return string
	 */
	public static function slug(BaseRepository $repository, $string, $primaryKey = null, $slugField = 'slug', $slugExtension = null) {
		$slug = \Nette\Utils\Strings::webalize($string);
		
		if($slugExtension !== null) { // slug already exits, iterating existing slug extension
			$slug .= ('-' . (string) ($slugExtension + 1));
		}
		
		$conditions = array($slugField => $slug);
		
		// we need to supply a primary key of the updating row when performing an update
		if($primaryKey) {
			$conditions["id <> ?"] = $primaryKey;
		}
		
		$entity = $repository->findOneBy($conditions);
		
		if($entity == FALSE) { // slug is unique, we can use it
			return $slug;
		}
		
		if(preg_match('/-([\d]+)$/', $entity->slug, $matches)) { // slug already contains numeric extension, we have to iterate that extension and try again
			return self::slug($repository, $string, $primaryKey, $slugField, $matches[1]);
		} else { // slug doesn't contain numeric extension, so we add one and try again
			return self::slug($repository, $string, $primaryKey, $slugField, 0);
		}
	}
}