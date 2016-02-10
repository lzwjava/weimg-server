package codereview
import (
	"testing"
	"os"
	"fmt"
	"crypto/md5"
	"database/sql"
	"strconv"
	_ "reflect"
	"net/url"
	_ "github.com/go-sql-driver/mysql"
)

func TestMain(m *testing.M) {
	os.Exit(m.Run())
}

func setUp() {
	cleanTables()
}

func cleanTables() {
	tables := []string{"comment_votes", "post_votes", "post_images", "posts", "images", "users"}
	deleteTable("comments", true)
	for _, table := range tables {
		deleteTable(table, false)
	}
	fmt.Println()
}

func checkErr(err error) {
	if err != nil {
		panic(err)
	}
}

func registerUser(c *Client) map[string]interface{} {
	res := c.post("users", url.Values{"mobilePhoneNumber": {"13261630925"},
		"username": {"lzwjavaTest"}, "smsCode": {"5555"}, "password":{md5password("123456")}})
	if (res["status"] == "success") {
		registerRes := res["result"].(map[string]interface{})
		c.sessionToken = registerRes["sessionToken"].(string)
		return registerRes
	} else {
		loginRes := login(c, "13261630925", "123456")
		return loginRes
	}
}

func login(c *Client, mobilePhoneNumber string, password string) map[string]interface{} {
	return c.postData("login", url.Values{"mobilePhoneNumber": {mobilePhoneNumber},
		"password":{md5password(password)}});
}

func md5password(password string) string {
	data := []byte(password)
	return fmt.Sprintf("%x", md5.Sum(data))
}

func deleteTable(table string, noCheck bool) {
	deleteRecord(table, "1", "1", noCheck);
}

func runSql(sentence string, noCheck bool) {
	db, err := sql.Open("mysql", "lzw:@/weimg")
	checkErr(err)

	err = db.Ping()
	checkErr(err)

	var stmt *sql.Stmt
	var res sql.Result

	if noCheck {
		stmt, err = db.Prepare("SET FOREIGN_KEY_CHECKS=0")
		checkErr(err)

		res, err = stmt.Exec()
		checkErr(err)
	}


	stmt, err = db.Prepare(sentence)
	checkErr(err)

	res, err = stmt.Exec()
	checkErr(err)

	affect, err := res.RowsAffected()
	checkErr(err)

	fmt.Println(sentence, "affected", affect)

	if noCheck {
		stmt, err = db.Prepare("SET FOREIGN_KEY_CHECKS=1")
		checkErr(err)

		res, err = stmt.Exec()
		checkErr(err)
	}

	db.Close()
}

func deleteRecord(table string, column string, id string, noCheck bool) {
	sqlStr := fmt.Sprintf("delete from %s where %s=%s", table, column, id)
	runSql(sqlStr, noCheck)
}

func toInt(obj interface{}) (int) {
	if _, isFloat := obj.(float64); isFloat {
		return int(obj.(float64))
	} else {
		return obj.(int)
	}
}

func floatToStr(flt interface{}) string {
	return strconv.Itoa(toInt(flt))
}
